/**
 * WordPress dependencies
 */
import { useEffect, useRef } from '@wordpress/element';
import { SelectControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { useSelect, useDispatch } from '@wordpress/data';
import { store as editorStore } from '@wordpress/editor';
import { store as coreStore } from '@wordpress/core-data';

const DEFAULT_QUERY = {
	per_page: -1,
	orderby: 'name',
	order: 'asc',
	_fields: 'id,name,parent,slug',
	context: 'view',
};

const PRIMARY_CATEGORY_META_KEY = '_10up_primary_category';

function PrimaryCategory( props ) {
	const {
		OriginalComponent,
	} = props;

	const {
		dbCategories,
		postMeta,
		postCategories,
		loading,
	} = useSelect( ( select ) => {
		return {
			dbCategories: select( coreStore ).getEntityRecords( 'taxonomy', 'category', DEFAULT_QUERY ),
			postMeta: select( editorStore ).getEditedPostAttribute( 'meta' ),
			postCategories: select( editorStore ).getEditedPostAttribute( 'categories' ),
			loading: select( coreStore ).isResolving( 'getEntityRecords', [
				'taxonomy',
				'category',
				DEFAULT_QUERY,
			] ),
		};
	}, [] );

	const { editPost } = useDispatch( editorStore );
	const setPostMeta = ( newMeta ) => editPost( { meta: newMeta } );

	/**
	 * Transform postCategories to be suitable for SelectControl.
	 */
	const postCategoriesOptions = ( !! dbCategories?.length ) !== false
		? postCategories.map( ( categoryId ) => {
			const category = dbCategories.find( ( dbCategory ) => dbCategory.id === categoryId );
			return { label: category?.name, value: category?.slug };
		} ) : [];

	/**
	 * If user removes a category from the post which is primary category,
	 * Update the primary category to be the first category in the list.
	 */
	useEffect( () => {
		if (
			!! postCategoriesOptions?.length &&
			! postCategoriesOptions.find( ( category ) => category.value === postMeta[ PRIMARY_CATEGORY_META_KEY ] )
		) {
			setPostMeta( { [ PRIMARY_CATEGORY_META_KEY ]: postCategoriesOptions[ 0 ].value } );
		} else if ( postCategoriesOptions?.length === 0 ) {
			setPostMeta( { [ PRIMARY_CATEGORY_META_KEY ]: '' } );
		}
	}, [ postCategories ] );

	/**
	 * If no category is selected when post loads, select "Uncategorized"
	 */
	const isMounted = useRef( false );
	useEffect( () => {
		if ( isMounted.current === false && postCategories.length === 0 && !! dbCategories?.length ) {
			const category = dbCategories.find( ( dbCategory ) => dbCategory.slug === 'uncategorized' ) || dbCategories[ 0 ];
			editPost( { categories: [ category.id ] } );
			isMounted.current = true;
		}
	} );

	return (
		<>
			<OriginalComponent { ...props } />
			<div style={ { marginTop: '10px' } } />
			{/* Render the Primary Category control below category control */}
			{ ! loading && <SelectControl
				label={ __( 'Primary Category:', '10up-primary-category' ) }
				value={ postMeta[ PRIMARY_CATEGORY_META_KEY ] }
				onChange={ ( primaryCategory ) => {
					setPostMeta( { [ PRIMARY_CATEGORY_META_KEY ]: primaryCategory } );
				} }
				options={ postCategoriesOptions }
			/> }
		</>
	);
}

export default PrimaryCategory;
