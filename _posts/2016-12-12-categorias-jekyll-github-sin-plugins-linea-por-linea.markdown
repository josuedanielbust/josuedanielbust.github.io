---
layout: post
title: Categorías en Jekyll y Github sin plugins - Linea por Linea
date: 2016-12-12 11:00:00
comments: true
categories: [development, desarrollo, github, jekyll]
image: /images/posts/categorias-en-jekyll-y-github-sin-plugins.png
---

### `_config.yml`

```yaml
# Collections
# Creating the_categories collection and set the output key
# to true allowing Jekyll to generate files .html for each file
# in _the_categories folder on the permalink destination folder
collections:
  the_categories:
    output: true
    permalink: /blog/category/:name/

# Defaults
# Setting the default layout value with the layout of the 5
# step generated on all the project for the_categories
# type (collection type)
defaults:
  -
    scope:
      path: “”
      type: the_categories
    values:
      layout: category_list
```

### `_the_categories/<filename>.md`

```markdown
# Setting the slug that is used for jekyll and the
# name that we show to the user on the blog
---
slug: development
name: Development
---
```

### `post.html`

```html
{% raw %}
<!-- Save all the block on the categories_content var -->
{% capture categories_content %}
    <!-- Walking through each item on page.categories (categories specified on each post) and site.the_categories (collection) -->
    {% for pcategory in page.categories %}
        {% for scategory in site.the_categories %}
            <!-- Only if the category is present on the post header is generated the html with the collection category info -->
            {% if pcategory == scategory.slug %}
                <span>
                    <a class=“label” href=“{{ scategory.url }}”>{{ scategory.name }}</a>
                </span>
            {% endif %}
        {% endfor %}
    {% endfor %}
{% endcapture %}

<!-- Showing to the user the block generated and saved on categories_content var -->
<div class=“post-categories”>
    <p>Posted in {{ categories_content }}</p>
</div>
{% endraw %}
```

### `category_list`

```html
{% raw %}
<!-- Set the layout as default layout (You can use your custom layout) -->
---
layout: default
---

<!-- If site.categories has info or posts about the current page (page.slug) show info else show and error -->
{% if site.categories[page.slug] %}
    <!-- Get the posts or info from site.categories thata have the actual category based on the slug of the page -->
    {% for post in site.categories[page.slug] %}
        <!— Your themeplate for the post loop goes here —>
        <p class=“post-title”>
            <a href=“{{ post.url | prepend: site.baseurl }}”>{{ post.title }}</a>
        </p>
    {% endfor %}
{% else %}
    <p>There are no posts in this category.</p>
{% endif %}
{% endraw %}
```