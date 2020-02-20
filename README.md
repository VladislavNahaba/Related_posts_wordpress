"# Related_posts_wordpress" 

Plugin for Wordpress, that shows related posts for current post.

Can be added automatically at the end of the post or by using shortcodes. Can be customizied by editing template or creating the new one.

If you want to add your own templates. You need to create php file in 'templates' folder with your own markup.

Templates variables:

$rel_item: global template data. Do it in cycle to get needed variables.

1. $rel_item['thumbnail'] - link to thumbnail image
2. $rel_item['link'] - link to post
3. $rel_item['text'] - text description of post
4. $rel_item['author'] - author name of post
5. $rel_item['date'] - publish date of post
