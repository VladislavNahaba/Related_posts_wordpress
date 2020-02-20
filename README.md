"# Related_posts_wordpress" 

Plugin for Wordpress, that shows related posts for current post.

Can be added automatically at the end of the post or by using shortcodes. Can be customizied by editing template or creating the new one.

Main template is standard.php in your plugin folder.

If you want to add and use your own template. You can create redpic_related_template.php file in your theme folder and configure it.
You also are able to create .php file in your theme folder with any name, but you will be able to use it only with shortcode.

[redpic_related template="FILE_FULL_NAME"]

Templates variables:

$rel_item: global template data. Do it in cycle to get needed variables.

1. $rel_item['thumbnail'] - link to thumbnail image
2. $rel_item['link'] - link to post
3. $rel_item['text'] - text description of post
4. $rel_item['author'] - author name of post
5. $rel_item['date'] - publish date of post
