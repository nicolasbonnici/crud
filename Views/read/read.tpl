{% if oEntity|Exists %}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h4 class="modal-title" id="myModalLabel">{{oEntity|safe}}</h4>
</div>
<div class="modal-body">
{% for sAttrName, sAttrValue in aEntityFields %}
    <p><strong>{{sAttrName|safe}}</strong> {{sAttrValue|safe}}</p>
{% endfor %}
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
</div>
{% else %}
<div class="alert alert-warning">
    <strong>Warning!</strong> Your role doesn't allow you to see this todo
</div>
{% endif %}
