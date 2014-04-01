{% if oEntities|Exists %} {% for oEntity in oEntities %}
<tr {% if oTodo.deadline>
    current_timestamp %} class="danger blackFontColor"{% endif %}>
    <td><input type="checkbox" class="ui-select" name="idtodo"
        value="{{oTodo.idtodo}}" /></td>
    <td>{{oTodo.label|safe}}</td>
    <td><span class="ui-timestamp"
        data-timestamp="{{oTodo.deadline}}"></span></td>
    <td>
        <div class="btn-group">
            <a href="#modal-todo" class="ui-sendxhr btn btn-info"
                data-url="/backend/crud/read/"
                data-selector="#modal-todo-content" data-entity="Todo"
                data-view="todo/read.tpl" data-toggle="modal"
                data-pk="{{oTodo.idtodo}}" title="{{tr['view']}}"><span
                class="glyphicon glyphicon-zoom-in"></span></a> <a
                href="#modal-todo" class="ui-sendxhr btn btn-warning"
                data-url="/backend/todo/update/"
                data-selector="#modal-todo-content" data-toggle="modal"
                data-idtodo="{{oTodo.idtodo}}" title="{{tr['edit']}}"><span
                class="glyphicon glyphicon-pencil"></span></a> <a
                href="#modal-todo" class="ui-sendxhr btn btn-danger"
                data-url="/backend/todo/delete/"
                data-selector="#modal-todo-content" data-toggle="modal"
                data-pk="{{oTodo.idtodo}}" title="{{tr['delete']}}"><span
                class="glyphicon glyphicon-trash"></span></a>
        </div>
    </td>
    <td>{{tr['last_edited']}}&nbsp;<span class="ui-timestamp"
        data-timestamp="{{oTodo.lastupdate}}"></span>
    </td>
</tr>
{% endfor %} {% else %}
<tr>
    <td>Aucun enregistrement</td>
</tr>
{% endif %}
