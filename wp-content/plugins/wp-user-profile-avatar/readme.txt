=== WP User Profile Avatar ===

Contributors: wpeventmanager,ashokdudhat,hiteshmakvana
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=55FRYATTFLA5N
Tags: avatar, user profile, gravatar,custom profile photo, custom profile picture, profile photo, profile picture, author image, author photo
Requires at least: 5.8
Tested up to: 6.5.4
Stable tag: 1.0.6
Requires PHP: 8.0.2
License: GNU General Public License v3.0
License URI: https://www.gnu.org/licenses/gpl-3.0.html

    
== Description ==


P User Profile Avatar allows you to change the default WordPress avatar or User profile picture. You can use any photos uploaded into your Media Library or use a custom photo URL as an avatar instead of using Gravatar.


**Plugin Features**

* **Lightweight and easy to use**.
* Upload an avatar image from media library or local computer.
* Set avatar image using custom path url.
* Visibility option whether show avatar or not.
* Display user profile avatar shortcode **[user_profile_avatar]**.
* Upload avatar shortcode **[user_profile_avatar_upload]**.
* Allow anyone *(Contributors & Subscribers)* can upload avatar.
* Disable Gravatar and use own custom avatars.
* You can rate avatar as G, PG, R, X based on your appropriateness.
* Allow to set default avatar. Default Avatar Allows you to pick a default image when no avatar exists.
* **SEO & Developer** Friendly.


**Set new user profile avatar from admin panel.**

* Go Admin Dashboard -> Users -> All Users --> Select any user profile you would like to edit. 

* Find "WP User Profile Avatar" section, You can give new avatar url path or you can upload avatar using media library.

* Update User.


**Display new user profile avatar at frontend side.**

To retrieve the user avatar/photo on the front-end use one of the following approach in your template page(s).  


*1. Using shortcode and pass parameters based on your need.*

     Shortcode: [user_profile_avatar]
     Parameters: userid, size, align, link, target and caption.
     userid : id of the user.
     size: original,medium,large,thumbnail.
     align: alignleft,aligncenter,alignright.
     link: image, attachment, custom.
     target: _blank, self,
     caption: you can give any text value in between shortcode bracket and it will display below user avtar profile.


    1.1  Display default avatar using visual editor.
        
        You can use the following shortcode in any page and it will show default avatar of the plugin.

        [user_profile_avatar] 

    1.2 Display avatar for the perticular user using visual editor.

        if you want to show user profile for the user id 1.

        [user_profile_avatar user_id="1"] 

        You can also set other parameters like size, align, link, target and caption in this shortcode.

        Example: 

        [user_profile_avatar user_id="1" size="original" align="aligncenter" link="image" target="_blank"] Display Name [/user_profile_avatar]

    1.3 if you want to assign dynamic user id in shortocde without using visual editor

`<?php
         //for the logged user, Current user object
        $user = wp_get_current_user();

        echo do_shortcode('[user_profile_avatar user_id="<?php $user->id ?>" size="original" align="aligncenter" link="image" target="_blank"]'. $user->display_name .'[/user_profile_avatar]');

?>`


*2. Using the function wpupa_get_url.*

You will need to place below code in each area of your theme where you wish to add and retrieve your theme’s custom avatar image. 


    2.1 At Author page
    -------------------

`<?php

    // Get The Post's Author ID
    $authorID = get_the_author_meta('ID');
    $authorname = get_the_author_meta('display_name', $authorID);

    // Set the image size. Accepts all registered images sizes and array(int, int)
    $size = 'thumbnail';
    $imgURL='';

    // Get the image URL using the author ID and image size params
    if (function_exists('wpupa_get_url'))  
        $imgURL = wpupa_get_url($authorID, ['size' => $size]);

    // display image on the page
    echo '<img src="'. $imgURL .'" alt="'. $authorname .'">';

?>`

    2.2 Except Author page
    ----------------------

`<?php

    // Current user object
    $user = wp_get_current_user();

    // Set the image size. Accepts all registered images sizes and array(int, int)
    $size = 'thumbnail';
    $imgURL='';

    // Get the image URL using the author ID and image size params
    if (function_exists('wpupa_get_url'))  
        $imgURL = wpupa_get_url($user->id, ['size' => $size]);

    // display image on the page
    echo '<img src="'. $imgURL .'" alt="'. $user->display_name .'">';

?>`

You will need to place above code in each area of your theme where you wish to add and retrieve your theme's custom avatar image. 

= Be a contributor =

If you want to contribute, go to our [WP User Profile Avatar GitHub Repository](https://github.com/wpeventmanager/wp-user-profile-avatar) and see where you can help.

You can also add a new language via [translate.wordpress.org](https://translate.wordpress.org/projects/wp-plugins/wp-user-profile-avatar). We've built a short guide explaining [how to translate and localize the plugin](https://www.wp-eventmanager.com/documentation/translating-wp-event-manager/).

Thanks to all of our contributors.


= Feedback =

* We are open for your suggestions and feedback - Thank you for using or trying out one of our plugins!
* [Drop us a line](http://wp-eventmanager.com/contact).

 
= More =
* [Also please have look our other plugins](http://wp-eventmanager.com/) or visit at [WordPress.org plugin page](https://wordpress.org/plugins/wp-event-manager/).

= Connect With US =

To stay in touch and get latest update about WP User Profile Avatar's further releases and features, you can connect with us via:

- [Facebook](https://www.facebook.com/wpeventmanager/)
- [Twitter](https://twitter.com/wp_eventmanager)
- [Google Plus](https://plus.google.com/u/0/b/107105224603939407328/107105224603939407328)
- [Linkedin](https://www.linkedin.com/company/wp-event-manager)
- [Pinterest](https://www.pinterest.com/wpeventmanager/)
- [Youtube](https://www.youtube.com/channel/UCnfYxg-fegS_n9MaPNU61bg)
 
== Installation ==



= Automatic installation =


Automatic installation is the easiest option as WordPress handles the file transfers itself and you don't even need to leave your web browser. To do an automatic install, log in to your WordPress admin panel, navigate to the Plugins menu and click Add New.


In the search field type "WP User Profile Avatar" and click Search Plugins. Once you've found the plugin you can view details about it such as the the point release, rating and description. Most importantly of course, you can install it by clicking _Install Now_.


= Manual installation =


The manual installation method involves downloading the plugin and uploading it to your web server via your favorite FTP application.


* Download the plugin file to your computer and unzip it

* Using an FTP program, or your hosting control panel, upload the unzipped plugin folder to your WordPress installation's `wp-content/plugins/` directory.

* Activate the plugin from the Plugins menu within the WordPress admin.


== Frequently Asked Questions ==


= How I can set new user profile avatar from admin panel? =

* Go Admin Dashboard -> Users -> All Users --> Select any user profile you would like to edit. 

* Find "WP User Profile Avatar" section, You can give new avatar url path or you can upload avatar using media library.

* Update User.

= How I can display new user profile avatar at frontend side? =

You can show user profile avatar two ways.

*1. Using shortcode and pass parameters based on your need.*

     Shortcode: [user_profile_avatar]
     Parameters: userid, size, align, link, target and caption.
     userid : id of the user.
     size: original,medium,large,thumbnail.
     align: alignleft,aligncenter,alignright.
     link: image, attachment, custom.
     target: _blank, self,
     caption: you can give any text value in between shortcode bracket and it will display below user avtar profile.


    1.1  Display default avatar using visual editor.
        
        You can use the following shortcode in any page and it will show default avatar of the plugin.

        [user_profile_avatar] 

    1.2 Display avatar for the perticular user using visual editor.

        if you want to show user profile for the user id 1.

        [user_profile_avatar user_id="1"] 

        You can also set other parameters like size, align, link, target and caption in this shortcode.

        Example: 

        [user_profile_avatar user_id="1" size="original" align="aligncenter" link="image" target="_blank"] Display Name [/user_profile_avatar]

    1.3 if you want to assign dynamic user id in shortocde without using visual editor

         //for the logged user, Current user object
        $user = wp_get_current_user();

        echo do_shortcode('[user_profile_avatar user_id="<?php $user->id ?>" size="original" align="aligncenter" link="image" target="_blank"]'. $user->display_name .'[/user_profile_avatar]');



*2. Using the function wpupa_get_url.*

You will need to place below code in each area of your theme where you wish to add and retrieve your theme’s custom avatar image. 


    2.1 At Author page
    -------------------

    // Get The Post's Author ID
    $authorID = get_the_author_meta('ID');
    $authorname = get_the_author_meta('display_name', $authorID);

    // Set the image size. Accepts all registered images sizes and array(int, int)
    $size = 'thumbnail';
    $imgURL='';

    // Get the image URL using the author ID and image size params
    if (function_exists('wpupa_get_url'))  
        $imgURL = wpupa_get_url($authorID, ['size' => $size]);

    // display image on the page
    echo '<img src="'. $imgURL .'" alt="'. $authorname .'">';

    2.2 Except Author page
    ----------------------

    // Current user object
    $user = wp_get_current_user();

    // Set the image size. Accepts all registered images sizes and array(int, int)
    $size = 'thumbnail';
    $imgURL='';

    // Get the image URL using the author ID and image size params
    if (function_exists('wpupa_get_url'))  
        $imgURL = wpupa_get_url($user->id, ['size' => $size]);

    // display image on the page
    echo '<img src="'. $imgURL .'" alt="'. $user->display_name .'">';


= How to allow Contributors & Subscribers can upload avatar? =

* Go Admin Dashboard -> Users -> Profile Avatar Settings.

* Find "Allow Contributors & Subscribers to upload avatars", check it.

* Save Changes.


== Screenshots ==


1. Settings.

2. Default Gavatars & set custom default avatar.

3. Set custom avatar for user profile example 1.

4. Set custom avatar for user profile example 2.

5. Display avatar & upload avatar shortcode icon for Visual Editor.

6. Generate display user profile avatar shortcode.

7. Generate user profile avatar upload shortcode.

8. Upload shortcode GUI for the frontend side.

9. After uploading avatar successfully, show undo option.

10. After refreshing page, remove option show instead of undo option for uploaded avatar.

11. After removing uploaded avatar, show message successfully removed avatar.

== Credits ==
1.WP Event Manager wants to give credit to GoogleAPIs as we are using GoogleAPIs to support users using their Google+ profile links on our plugin.
2.  WP Event Manager wants to give credit to Gravatar as we are using [Gravatar](https://gravatar.com/) to support users using their Gravatar profile links on our plugin. Privacy policy page of [Gravatar](http://automattic.com/privacy/).

== Changelog ==

= 1.0.6 [Sept 26th, 2024] =

Fixed : Change Gravatar Logo to Mystery Man option issue is fixed.
Fixed : Default image avatar issue is fixed.
Fixed : After deleting a comment count is removed.
Fixed : Selected options are unchecked when click on the save change button.
Fixed : Wordpress ORG notice is fixed.
Fixed : Image URL avatar is not set.
Fixed : Elementor with wp user profile avatar author box option is not working.
Added : Reset button is added to comment & Delete section.
Added : Disabled & Delete Comment options are added.

= 1.0.5 [Sept 9th, 2024] =

Fixed: Admin profile is not display.
Fixed: After updating plugin user profile image as set Mystery Man image.
Fixed: User Profile is not update from User Profile Avatar Settings Page.
Fixed: Correct setting name.
Fixed: Delete comment - Post is unable to delete.
Fixed: Delete Comment - Check marked again visible in the box.
Fixed: Admin profile image is missing from comment.

= 1.0.4 [July 19th, 2024] =

Fixed: The Avatar spelling is rectified.
Fixed : Deprecated code error is fixed.
Fixed : Check marked again visible in the box.
Fixed: Profile image remains the same when changing user name.
Fixed: Issue related to the “Avatar link to” option is resolved.
Fixed: Adding shortcode-related problem is resolved.
Fixed: New profile image is no more visible after refreshing the page.
Fixed: No undefined text is visible in the size attribute value.
Fixed: Shortcode attribute-related issue is resolved.
Fixed: Proper user image is now displayed in each user profile avatar.
Fixed: Issue related to the option “open link in a new window” is resolved.
Fixed: Space is added between the two buttons.
Fixed: User role is now saved properly.
Fixed: The issue related to the appearance of a new image when the update button is pressed is resolved.
Fixed: The image button is not visible after removing an image.
Fixed: The Undo button is now visible on the shortcode page.
Fixed: The blue default user icon is now visible.
Fixed : Comment enable/disable issue is fixed.

= 1.0.3 [June 11th, 2024] =

Fixed: The security issue of the plugin has been resolved.
Fixed: The issue of displaying a corrupted author image at the backend has been resolved.
Fixed: The remove image button is now visible.
Fixed: The issue related to displaying the profile image on the event, organizer, and venue pages is now resolved.
Fixed: The image URL avatar is now set.
Fixed: The user dropdown box now displays the default text.

= 1.0.2 [Feb 22nd, 2023] =

Fixed: User section text is now displayed properly.
Fixed: The Mystery Man image is now set at the user section.
Fixed: The text related to the biographical info in Elementor is now displayed at the correct place.
Fixed: All the options are set for the default user profile avatar.
Fixed: Now p tags are created with social media options.
Fixed: Space is added to the author box.
Fixed: The “choose” button in the default avatar section is now working properly.
Fixed: The user avatar size reduction option is now working.
Fixed: The security issue is now resolved.
Fixed: The profile image display related issue is now resolved.
Fixed: Now custom default profile avatar can be set after disabling the default gravatar.
Added: The option to hide the Bio Info Author Box is added.

= 1.0.1 [Dec 15th, 2023] =

Fixed : No error message is shown while updating the username from the admin panel
Fixed : Security issues are resolved in the profile avatar settings.
Fixed : Security vulnerability issue in the profile avatar plugin is resolved.
Fixed : The title of a settings option is rectified.
Fixed : Proper user profile avatar description is added.
Fixed : The active license option is removed from Profile Avatar.
Fixed : User profile menu options are now available.
Fixed : Issue related to the URL image size in the Elementor is resolved now.
Fixed : Biographical info is removed from the venue and organizer section.
Fixed : Profile Avatar is now visible on the event details page.
Fixed : The warning message that appears in the backend is removed.
Fixed : The profile avatar size issue in the Elementor is resolved.
Fixed : The issue related to uploading an image in profile avatar in elementor has been resolved.
Fixed : Additional tooltip display issue in the profile icon is resolved now.
Fixed : Profile icon alignment issue is resolved.
Fixed : Profile Avatar banner is improved in wordpress.org.
Fixed : Fields are removed after uninstalling the plugin.
Fixed : Username label in profile avatar is rectified.
Fixed : Issue related to the plugin activation is resolved.
Fixed : Images on the All user page are now properly displayed.
Fixed : Administrator’s media library access is restricted for other users.
Fixed : Issue related to controlling the file size is now resolved.
Fixed : Homepage error is resolved.
Fixed : Authorbox is now available with a Social media link.
Fixed : The plugin is now compatible with bbpress.
Fixed : Shortcode avatar button is added to the visual editor.

= 1.0 [May 23th, 2020] =

First stable release of User Profile Avatar.
