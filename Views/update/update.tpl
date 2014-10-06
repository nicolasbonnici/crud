{% if bUpdateEntity|Exists %} {% if bUpdateEntity %}
<h4>Succès!</h4>
<p>L'enregistrement a correctement été mis à jour.</p>
{% else %}
<h4>Une erreur est survenue...</h4>
<p>Impossible de mettre à jour l'enregistrement, une erreur est survenue...</p>
{% endif %} {% endif %}
