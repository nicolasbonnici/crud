{% if bCreateEntity|Exists %} {% if bCreateEntity %}
<h4>Succès!</h4>
<p>Votre vous pouvez éditer dès à présent votre nouvel enregistrement.</p>
{% else %}
<h4>Une erreur est survenue...</h4>
<p>Une erreur empèche le nouvel enregistrement, veuillez vous assurer que les champs sont correctement remlis.</p>
{% endif %} {% endif %}
