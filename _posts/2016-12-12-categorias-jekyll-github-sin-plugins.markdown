---
layout: post
title: Categorías en Jekyll y Github sin plugins
date: 2016-12-12 10:30:00
comments: true
categories: [development, desarrollo, github, jekyll]
image: /images/posts/categorias-en-jekyll-y-github-sin-plugins.png
---

Hace unos meses atrás quise incluir las categorías en mi blog para organizar los temas y los posts. Me imagino que ya sabes que desde el núcleo de [Jekyll](https://jekyllrb.com) tenemos una integración de Categorías y Etiquetas muy básica en la cual hace falta la vista y el controlador de estas.

Para lograr hacer la integración de las categorías toca recurrir a plugins o a otras formas pero el problema se presenta cuando te ves limitado a que [GitHub](https://github.com) tiene un lista de plugins para [Jekyll](https://jekyllrb.com) permitidos por default.

Intentando hacerlo la primera vez y sin saber esto de la lista blanca de plugins lo logre implementar localmente con un [plugin que fácilmente encontré en Github](https://github.com/recurser/jekyll-plugins) pero me encontré con el problema cuando hice el `git push` que no cargaba en realidad nada y es en la búsqueda de la solución a esta que me encontré con la [lista blanca o de dependencias](https://pages.github.com/versions/) de Jekyll en Github.

Ahora en mi segunda vez tratando de lograr el objetivo de integrar las Categorías en mi blog me encontré con un [post en el blog de minddust](http://www.minddust.com/post/alternative-tags-and-categories-on-github-pages/) que tiene una muy buena integración si solo vas a usar una Categoría para cada post pero cuando piensas usar más de una Categoría se queda corto.

La diferencia entre usar una o multiples categorías se encuentra en el uso de la llave que se quiera usar en los posts.

* `category` la forma singular de la palabra categoría, genera un string del valor.

```markdown
# Single category
---
title: “Categorías en Jekyll y Github sin plugins”
date: 2016-12-12 11:00:00
category: "development"
---
```

* `categories` la forma plural de la palabra categoría, genera un array de strings de los valores

```markdown
# Multiples categories
---
title: “Categorías en Jekyll y Github sin plugins”
date: 2016-12-12 11:00:00
categories: [development, github, jekyll]
---
```


Para esta integración usaremos los conceptos de [colecciones de Jekyll](https://jekyllrb.com/docs/collections/).

---

1. Editaremos el archivo `_config.yml`

```yml
# Collections
collections:
  the_categories:
    output: true
    permalink: /blog/category/:name/

# Defaults
defaults:
  -
    scope:
      path: ""
      type: the_categories
    values:
      layout: category_list
```

2. Crearemos en el root la carpeta `_the_categories`
3. Para cada nueva categoría que se quiera generar hay que crear un archivo markdown en la carpeta que recien creamos
(El nombre de archivo debe coincidir con la llave slug del archivo)

```markdown
---
slug: development
name: Development
---
```

4. Añadiremos un poco de lógica a nuestro layout `post.html` para agregar las etiquetas a nuestro post

```html
{% raw %}
{% capture categories_content %}
    {% for pcategory in page.categories %}
        {% for scategory in site.the_categories %}
            {% if pcategory == scategory.slug %}
                <span><a class="label" href="{{ scategory.url }}">{{ scategory.name }}</a></span>
            {% endif %}
        {% endfor %}
    {% endfor %}
{% endcapture %}

<div class="post-categories">
    <p>Posted in {{ categories_content }}</p>
</div>
{% endraw %}
```

5. Añadiremos un nuevo layout `category_list` para generar una página para cada categoría con sus posts

```html
{% raw %}
---
layout: default
---
{% if site.categories[page.slug] %}
    {% for post in site.categories[page.slug] %}
        <p class="post-title"><a href="{{ post.url | prepend: site.baseurl }}">{{ post.title }}</a></p>
        <!-- Your themeplate for the post loop goes here -->
    {% endfor %}
{% else %}
    <p>There are no posts in this category.</p>
{% endif %}
{% endraw %}
```


---


Con esto ya tendremos nuestras categorías agregadas a nuestro blog en Jekyll.

Si quieres conocer sobre la lógica y el porque de cada linea de código agregada te invito a ver la [continuación](http://josuedanielbust.com/blog/2016-12/categorias-jekyll-github-sin-plugins.html) de este post.