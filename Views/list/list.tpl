{% if oEntities|Exists %} 
{% for oEntity in oEntities %}
<div class="col-md-12">
{{oEntity}}
</div>
{% endfor %} 
{% else %}
<div class="col-md-12">
    <div class="alert alert-info">
        <p><span class="glyphicon glyphicon-warning-sign">Aucun enregistrement trouvé...</span></p>
    </div>
</div>
{% endif %}
