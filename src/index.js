/**
 * WordPress dependencies
 */
import { addFilter } from '@wordpress/hooks';
import domReady from '@wordpress/dom-ready';

/**
 * Internal dependencies
 */
import PrimaryCategory from './PrimaryCategory';

domReady( () => {
	/**
	 * Hook into the post taxonomy block to add our primary category block.
	 */
	addFilter(
		'editor.PostTaxonomyType',
		'tenup-primary-category',
		( OriginalComponent ) => {
			return ( props ) => {
				/**
				 * Only render primary category block on category taxonomy(and not post tags).
				 */
				if ( props.slug === 'category' ) {
					return <PrimaryCategory
						OriginalComponent={ OriginalComponent }
						{ ...props }
					/>;
				}
				return <OriginalComponent { ...props } />;
			};
		},
	);
} );
