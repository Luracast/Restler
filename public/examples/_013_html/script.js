$(document).ready(function () {
    /* The following code is executed once the DOM is loaded */

    $(".todoList").sortable({
        axis : 'y',        // Only vertical movements allowed
        containment : 'window',    	// Constrained by the window
        update : function (event, ui) {    // The function is called after the todos are rearranged
            id = ui.item[0].id;

            // The toArray method returns an array with the ids of the todos
            var arr = $(".todoList").sortable('toArray');

            $.ajax({
                url: 'tasks/' + id.replace('todo-', '') + '.json',
                contentType: 'application/json',
                type: 'PATCH',
                dataType: 'json',
                data: '{"position": ' + arr.indexOf(id) + '}'
            });
        },

        /* Opera fix: */

        stop : function (e, ui) {
            ui.item.css({'top' : '0', 'left' : '0'});
        }
    });

    // A global variable, holding a jQuery object 
    // containing the current todo item:

    var currentTODO;

    // Configuring the delete confirmation dialog
    $("#dialog-confirm").dialog({
        resizable : false,
        height : 130,
        modal : true,
        autoOpen : false,
        buttons : {
            'Delete item' : function () {
                $.ajax({
                    url : 'tasks/' + currentTODO.data('id') + '.html',
                    type : 'DELETE',
                    success : function (msg) {
                        currentTODO.fadeOut('fast');
                    }
                });
                $(this).dialog('close');
            },
            Cancel : function () {
                $(this).dialog('close');
            }
        }
    });

    // When a double click occurs, just simulate a click on the edit button:
    $('.todo').live('dblclick', function () {
        $(this).find('a.edit').click();
    });

    // If any link in the todo is clicked, assign
    // the todo item to the currentTODO variable for later use.

    $('.todo a').live('click', function (e) {

        currentTODO = $(this).closest('.todo');
        currentTODO.data('id', currentTODO.attr('id').replace('todo-', ''));

        e.preventDefault();
    });

    // Listening for a click on a delete button:

    $('.todo a.delete').live('click', function () {
        $("#dialog-confirm").dialog('open');
    });

    // Listening for a click on a edit button

    $('.todo a.edit').live('click', function () {

        var container = currentTODO.find('.text');

        if (!currentTODO.data('origText')) {
            // Saving the current value of the ToDo so we can
            // restore it later if the user discards the changes:

            currentTODO.data('origText', container.text());
        }
        else {
            // This will block the edit button if the edit box is already open:
            return false;
        }

        $('<input type="text">').val(container.text()).appendTo(container.empty());

        // Appending the save and cancel links:
        container.append(
            '<div class="editTodo">' +
                '<a class="saveChanges" href="#">Save</a> or <a class="discardChanges" href="#">Cancel</a>' +
                '</div>'
        );

    });

    // The cancel edit link:

    $('.todo a.discardChanges').live('click', function () {
        currentTODO.find('.text')
            .text(currentTODO.data('origText'))
            .end()
            .removeData('origText');
    });

    // The save changes link:

    $('.todo a.saveChanges').live('click', function () {
        var text = currentTODO.find("input[type=text]").val();

        $.ajax({
            url: 'tasks/' + currentTODO.data('id') + '.json',
            contentType: 'application/json',
            type: 'PATCH',
            dataType: 'json',
            data: '{"text": "' + text + '"}'
        });

        currentTODO.removeData('origText')
            .find(".text")
            .text(text);
    });


    // The Add New ToDo button:

    var timestamp = 0;
    $('#addButton').click(function (e) {

        // Only one todo per 5 seconds is allowed:
        if ((new Date()).getTime() - timestamp < 5000) return false;

        $.post("tasks.html", {'text' : 'New Task. Double Click to Edit.', 'rand' : Math.random()}, function (msg) {

            // Appending the new todo and fading it into view:
            $(msg).hide().appendTo('.todoList').fadeIn();
        });

        // Updating the timestamp:
        timestamp = (new Date()).getTime();

        e.preventDefault();
    });

}); // Closing $(document).ready()