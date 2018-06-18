$(document).on('click', 'a[data-modal]', (e) => {
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();

    let button = $(e.currentTarget);
    let url = button.attr('href');

    let modal = $('.ui.modal');
    modal.hide();

    $.ajax({
        url: url,
        success: (html) => {
            let modal = $('.ui.modal');

            modal.find('.header').html(button.attr('title'));

            modal
                .find('.content')
                .html(html);

            modal.find('.content .negative, .content .deny, .content .cancel').on('click', (e) => {
                $(e.currentTarget).closest('.ui.modal').modal('hide');
            });

            modal.find('.actions').remove();

            $('.ui.checkbox').checkbox();
            $('.ui.dropdown').dropdown();

            modal.modal('show');
        }
    })
});
