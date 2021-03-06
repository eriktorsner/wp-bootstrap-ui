
# wp-bootstrap
Utils for bootstrapping a WordPress installations. Automates installation, configuration and content bootstrapping of WordPress installation.

Wp-bootstrap depends on [wp-cli](http://wp-cli.org/) and a plugin named [WP-CFM](https://wordpress.org/plugins/wp-cfm/) to do a lot of the heavy lifting under the hood. The ideas and rationale that inspired this project was originally presented on [my blog](http://erik.torgesta.com/) and  in the book [WordPress DevOps](https://leanpub.com/wordpressdevops) available on [Leanpub](https://leanpub.com/wordpressdevops).  Wp-bootsrap also assumes that you are using Composer even if it's not strictly needed.

Wp-bootstrap uses two configuration files, appsettings.json and localsettings.json to control it's behaviour, all data is stored in subfolder "bootstrap" under the project root.

Besides being able to configure and script the setup of a target environment, one of the main goals with with wp-bootstrap is to be able to **export** settings, pages, menus etc. from a WordPress development environment and later to **import** that data into a staging or production WordPress environment. Pages, menus or taxonomy terms that already exists on the target installation will be updated. 

This project scratches a very specific WordPress itch: being able to develop locally, managing the WordPress site in Git and being able to push changes (releases) to a production environment without worrying about overwriting content or having to manually migrate any setting or content. The workflow is intended to be:

#### On the development server (hint: use Vagrant):

 - Start a new project by requiring wp-bootstrap in composer.json
 - Run vendor/bin/wpboostrap wp-init-composer to get easier access to the wp-bootstrap commands
 - Create a localsettings.json and appsettings.json
 - Make sure you exclude localsettings.json from source code control
 - Initiate the development installation with commands `composer wp-install` and `composer wp-setup`
 - As settings are updated, use the WP-CFM interface in WordPress Admin to include the relevant settings into the application configuration
 - As plugins and themes are needed, add them to appsettings.json and rerun the wp-setup command to get them installed into your local environment
 - As posts and menus are added, include them in appsettings.json.
 - When it's time to deploy to a staging or production environment, run `composer wp-export` command to get all content serialized to disk. Add them to your Git repo

#### On the staging or production server:

  - Create the local database
  - Check out the project from Git
  - Create up your localsettings.json file with the relevant passwords and paths.
  - Run composer update
  - Run vendor/bin/wpboostrap wp-init-composer to get easier access to the wp-bootstrap commands
  - Run `composer wp-install`, `composer wp-setup` and `composer wp-import`

Once the target environment has been setup, new changes from the development environment can be pushed by checking out the new changes using Git and rerunning `wp-setup` and `wp-import`.

## Installation

To add this package as a local, per-project dependency to your project, simply add a dependency on `eriktorsner/wp-bootstrap` to your project's `composer.json` file. Here is a minimal example of a `composer.json` file that just defines a dependency wp-bootstrap:

    {
        "require": {
            "eriktorsner/wp-bootstrap": "0.2.*"
        }
    }

**Note!:** wp-bootstrap assumes that wp-cli is globally available on the machine using the alias "wp". 

### Quick start

Wp-bootstrap can be called directly from it's binary, located in vendor/bin. To reduce typing, you can add the bootstrap commands to your composer file:

    $ vendor/bin/wpbootstrap wp-init-composer
    
Then to run a command:

    $ composer wp-export
    

## Commands

wp-bootstrap exposes a few command that can be called from various automation envrionments

| Command | Arguments | Decription |
|---------|------------|------------|
| wp-install || Download and install WordPress core. Creates a default WordPress installation |
| wp-setup  || Add themes and plugins and import content |
| wp-bootstrap || Alias for wp-install followed by wp-setup|
| wp-update |none, "themes" or "plugins"| Updates core, themes or plugins that are installed from the WordPress repos |
| wp-export || Exports content from the WordPress database into text and media files on disk|
| wp-import || Imports content in text and media files on disk into the database. Updates existing pages / media if it already exists |
| wp-pullsettings || Helper: Adds a wpbootstrap section to the appsettings file (if it doesn't already exist) |

## Usage

wp-bootstrap is intended to be executed from a script or task runner like Grunt, Gulp or Composer. It can be called directly from the command line or as part of a larger task in i.e Grunt:

**Command line usage:**

    $ vendor/bin/wpbootstrap wp-export
    $ vendor/bin/wpbootstrap wp-update plugins


**Grunt usage:**
Sample Grunt setup (just showing two methods):

    grunt.registerTask('wp-export', '', function() {
        cmd = "vendor/bin/wpbootstrap wp-export";
        shell.exec(cmd);
    });

    grunt.registerTask('wp-update', '', function() {
        cmd = "vendor/bin/wpbootstrap wp-update";
        if (typeof grunt.option('what') != 'undefined') cmd += ' ' + grunt.option('what');
        shell.exec(cmd);
    }); 

Then run your grunt task like this:

    $ grunt wp-export
    $ grunt wp-update --what=plugins



**Composer usage:**
Wp-bootstrap can be added to your composer.json if you prefer to use composer as a task runner. You can manually edit your composer.json to include be below script entries:

    "scripts": {
        "wp-bootstrap": "vendor\/bin\/wpbootstrap wp-bootstrap",
        "wp-install": "vendor\/bin\/wpbootstrap wp-install",
        "wp-setup": "vendor\/bin\/wpbootstrap wp-setup",
        "wp-update": "vendor\/bin\/wpbootstrap wp-update",
        "wp-export": "vendor\/bin\/wpbootstrap wp-export",
        "wp-import": "vendor\/bin\/wpbootstrap wp-import",
        "wp-pullsettings": "vendor\/bin\/wpbootstrap wp-updateAppSettings",
        "wp-init": "vendor\/bin\/wpbootstrap wp-init",
        "wp-init-composer": "vendor\/bin\/wpbootstrap wp-initComposer"
    }

Or, have wp-bootstrap edit your composer for you:

    $ vendor/bin/wpbootstrap wp-init-composer


Then run a method from the cli like this:

    $ composer wp-install
    $ composer wp-update plugins    



## Settings 

wp-bootstrap relies on 2 config files in your project root

**localsettings.json:**

    {
        "environment": "development",
        "url": "www.wordpressapp.local",
        "dbhost": "localhost",
        "dbname": "wordpress",
        "dbuser": "wordpress",
        "dbpass": "wordpress",
        "wpuser": "admin",
        "wppass": "admin",
        "wppath": "/vagrant/www/wordpress-default"
    
    }

The various settings in localsettings.json are self explanatory. This file is not supposed to be managed in source code control but rather be unique for each server where your WordPress site is installed (development, staging etc). 

**appsettings.json:**

    {
        "title": "Your WordPress site title",
        "plugins": {
            "standard": [
                "google-analyticator",
                "if-menu:0.2.1"
            ],
            "local": [
                "myplugin"
            ]
        },
        "themes": {
            "standard": [
                "twentyfourteen"
            ],
            "local": [
                "mychildtheme"
            ],
            "active": "mychildtheme"
        },
        "settings": {
            "blogname": "New title 2",
            "blogdescription": "The next tagline"
        },
        "wpbootstrap": {
            "posts": {
                "page": [
                    "about",
                    "members"
                ]
            },
            "menus": {
                "main": [
                    "primary",
                    "footer"
                ]
            },
            "taxonomies": {
                "category": "*"
            },
            "references": {
                "posts": {
                    "options": [
                        "some_setting",
                        {
                            "mysettings": "->term_id"
                        },
                        {
                            "mysettings2": "[2]"
                        },
                        {
                            "mysettings3": [
                                "->term_id",
                                "->other_term_id"
                            ]
                        }
                    ]
                }
            }
        }
    }

### Section: plugins:
This section consists of two sub arrays "standard" and "local". Each array contains plugin names that should be installed and activated on the target WordPress site. 

 - **standard** Fetches plugins from the official WordPress repository. If a specific version is needed, specify the version using a colon and the version identifier i.e **if-menu:0.2.1**
 - **local** A list of plugins in your local project folder. Plugins are expected to be located in folder projectroot/wp-content/plugins/. Local plugins are symlinked into place in the wp-content folder of the WordPress installation specified by wppath in localsettings.json

### Section: themes
Similar to the plugins section but for themes. 

 - **standard** Fetches themes from the official WordPress repository. If a specific version is needed, specify the version using a colon and the version identifier i.e **footheme:1.1**
 - **local** A list of themes in your local project folder. The themes are expected to be located in folder projectroot/wp-content/themes/. Local themes are symlinked into place in the wp-content folder of the WordPress installation specified by wppath in localsettings.json
 - **active** A string specifying what theme to activate.


### Section: settings

A list of settings that will be applied to the WordPress installation using the wp-cli command "option update %s". Currently only supports simple scalar values (strings and integers)

###Section: wpbootstrap

This sections defines how to handle content during export and import of data using the wp-export or wp-import command. 

**posts** Used during the export process. Contains zero or more keys with an associated array. The key specifies a post_type (page, post etc) and the array contains **post_name** for each post to include. The export process also includes any media (images) that are attached to the specific post.

**menus** Used during the export process. Contains zero or more keys with an associated array. The key represents the menu name (as defined in WordPress admin) and the array should contain each *location* that the menu appears in. Note that location identifiers are unique for each theme.

**taxonomies** Used during the export process. Contains zero or more keys with either a string or array as the value. Use asterisk (*) if you want to include all terms in the taxonomy. Use an array of term slugs if you want to only include specific terms from that taxonomy.

**references** Used during the import process. This is a structure that describes option values (in the wp_option table) that contains references to a page or a taxonomy term. The reference item can contain a "posts" and a "terms" object describing settings that points to either posts or taxonomy terms. Each of these objects contains one single member "options" referring to the wp_options table (support for other references will be added later). The "options" member contains an array with names of options in the wp_option table. There are three ways to refer to an option:

  - **1.** A simple string, for instance "page_on_front". Meaning that there is an option in the wp_options table named "page_on_front" and that option is a reference to a post ID.
  - **2.** An object with a single name-value pair, for instance {"mysetting": "[2]"} or {"mysetting2":"->page_id"} meaning:
    -  There is an option in the wp_options table named "mysetting"
    - That setting is an array or object and the value tells wp-bootstrap how to access the array element or member variable of interest. The value follows PHP syntax, so an array element is accessed via "[]" notation and an object member variable is accessed via the "->" syntax.
  - **3.** As above, but instead of a simple string value, the value is an array of strings.

Reference resolving will only look at the pages/posts/terms included in your import set.  The import set might include an option "mypage" in the config/wpbootstrap.json file that points to post ID=10. Also in the import set, there is that page with id=10. When this page is imported in the target WordPress installation, it might get anohter ID, 22 for instance. By telling wp-bootstrap that the setting "mypage" in the wp_options table refers to a page, wp-bootstrap will update that option to the new value 22 as part of the process.

###Parent child references and automatic includes

Wp-bootstrap tries it's hardest to preseve references between exported WordPress objects. If you export a page that is the child of another page, the parent page will be included in the exported data regardless if that page was included in the settings. Similar, if you export a menu that points to a page or taxonomy term was not specified, that page and taxonomy term will also be included in the exported data. 

###Import matching
When importing into a WordPress installation, wp-bootstrap will use the **slug** to match pages, menus and taxonomy terms. So if the dataset to be imported contains a page with the **slug** 'foobar', that page will be (a) created if it didn't previously exist or (b) updated if it did. The same logic applies to posts (pages, attachments, posts etc), menu items and taxonomy terms.

**Note:** Taxonomies are defined in code rather than in the database. So the exact taxonomies that exist in a WordPress installation are defined at load time. The built in taxonomies are always there, but some taxonomies are defined in a theme or plugin. In order for your taxonomy terms to be imported during the wp-import process, the theme or plugin that defined the taxonomy needs to exist.


## Testing

Since wp-bootstrap relies a lot on WordPress, there's a separate Github repository for testing using Vagrant. The test repo is available at [https://github.com/eriktorsner/wp-bootstrap-test](https://github.com/eriktorsner/wp-bootstrap-test).

## Contributing

Contributions are welcome. Apart from code, the project is in need of better documentation, more test cases, testing with popular themes and plugins and so on. Any type of help is appreciated.

## Version history

**0.2.2** 

  - Support for ***references***. Possible to add names of options that are references to other posts or taxonomy terms. 
  - Fixed issues found when  Test coverage up to over 80%. 
  

**0.2.1**  

 - Support for taxonomy terms

