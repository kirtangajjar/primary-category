# 10 Up Primary Category

This plugin allows you to select primary category in the backend and lets you filter posts by them in frontend.

## Features

* Primary Category dropdown in Gutenberg editor.
* Filter Posts by primary category in frontend.
* Filter Posts by all categories in original category page.
* Post permalinks respecting primary category(if selected).

## Thought Process and Assumptions

Since this particular exercise left implementation details out so we can have a chance to show us strategic
thinking, I'd like to share my thoughts.

Through my time as a WordPress developer, I've come across a few projects where we had such a need to filter posts by primary category. Most of the time, it was done by large publishers who wanted the primary category of the post to be in the permalink for SEO purposes. Also, during my research on primary category features, I found there were several popular plugins which offer this feature like Yoast SEO, RankMath etc. So I proceeded with this assumption that the plugin will handle this use-case where the primary category selected in the backend will come in permalink of the post, and this will also be the primary way in which the user will be able to filter posts by the primary category. So I'm assuming here that the user will manually update their permalink structure to `/%category%/%postname%/` in the WordPress admin after plugin activation.

Also, given that Gutenberg is the future of WordPress, I'm assuming that the site on which the plugin is installed will also be using Gutenberg editor. I've not handled Classic editor case.

## Installation

1. Clone this repo in your WordPress plugins directory.
2. Run the following commands from the repo:
```
composer install
npm i && npm run build
```
3. Activate the plugin in the WordPress admin.
4. Set permalink structure to `/%category%/%postname%/` in the WordPress admin.

## Usage and Manual Testing

1. This plugin will add a new field in gutenberg to the post edit screen below categories called "Primary Category". The values in this dropdown will change in real-time based on the categories selected in the post.
2. Select multiple categories and a primary category and Save the post.
3. Open "Post Preview". You will see that the URL will have primary category in the permalink.
4. Try changing the URL to another category. You will see that the browser will redirect to the primary category permalink.
5. Open a custom category page i.e. `http://example.com/category-name/`. You will see that here, the posts will be filtered by the primary category.
6. Open original category archive page i.e. `http://example.com/category/category-name/` and you will see that here, the posts will *not* be filtered by the primary category. Here you will see all the posts in the category. This behaviour has been intentionally provided by the plugin to retain original behaviour of category pages incase the user needs it.

A video walkthrough of the plugin can be found [here](https://vimeo.com/708736382/4207c3054a).

## Automated Testing

The code also has unit tests in the `tests/` directory. Here is how to run them:
1. Go to the root directory of the plugin.
2. Run `bash bin/install-wp-tests.sh <database> <user> <pass> <host> <wp-version>`
3. Run `vendor/bin/phpunit`

## Coding Standards

The project uses WordPress and WordPress VIP coding standards. You can see the coding standards configuration in the `phpcs.xml.dist` file.

## Time Tracking

* Initial research on primary category and thinking how to approach the problem                          -  **1hr**
* Finalising tech stack and initial scaffolding                                                          -  **.5hr**
* Adding dropdown in gutenberg. This involved research on the best way to do it followed by development. -  **5hr**
* Backend work on the plugin. Registering the CPT and adding filters                                     -  **2hr**
* Adding unit tests                                                                                      -  **1hr**

Total time spent on this project is **9.5** hours.
