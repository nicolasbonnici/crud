{% if oEntity|Exists %}
<div class="modal-header">
    <button
        type="button"
        class="close"
        data-dismiss="modal"
        aria-hidden="true">×</button>
    <h4
        class="modal-title"
        id="myModalLabel">{{oTodo.label|safe}}</h4>
</div>
<div class="modal-body">{{oTodo.content|safe}}</div>
<div class="modal-footer">
    <button
        type="button"
        class="btn btn-default"
        data-dismiss="modal">Fermer</button>
</div>
{% else %}
<div class="alert alert-warning">
    <strong>Warning!</strong> Your role doesn't allow you to see this todo
</div>
{% endif %}
