
function pagination(e, post_type) {

    let template = $('link-pagination').attr('data-template');
    let currentTemplate = '';

    $('.pagination-link').removeClass('active-pagination');

    $(e).addClass('active-pagination');

    window.currentPage = parseInt($(e).text());
    let container = $('#container');
    let paginationContainer = $('#pagination-container');

    $.ajax({
        url : '/wp-content/themes/mytheme/handlers/pagination-handler.php',
        method: 'post',
        data: {
            'offset'   : $(e).attr('data-page'),
            'post_type': post_type
        },
        success: function (response) {
            response = JSON.parse(response);
            console.log(response);
            paginationContainer.children('.flex').remove();

            paginationContainer.append(response[0]);
            container.children().remove();

            for (let i = 0; i < response[1].length; i++) {

                let title         = response[1][i].post_title;
                let content       = response[1][i].post_content;
                let postThumbnail = response[1][i].thumbnail;
                let link          = response[1][i].link;
                let city          = response[1][i].city;
                let postDate      = response[1][i].date;

                if (content.length > 1500) {
                    content = content.substring(0, 1500);
                }


                switch (template) {
                    case 'text-img-date':
                        currentTemplate = '<div class="executive-block flex flex-start">\n' +
                            '                    <a href="'+ link +'" class="left-column-thumbnail block">\n' +
                            '                        <img class="adaptive-img-full-width effect-img" src="'+ postThumbnail +'" alt="Новости">\n' +
                            '                    </a>\n' +
                            '                    <div class="right-column-content">\n' +
                            '                        <div class="flex addition-option justify-between">\n' +
                            '                            <p class="date-published">'+ postDate +'</p>\n' +
                            '                            <p class="current-city-published-news">'+ city +'</p>\n' +
                            '                        </div>\n' +
                            '                        <p class="title-executive">'+ title +'</p>\n' +
                            '                        <div class="content-executive">'+ content +'</div>\n' +
                            '                        <a class="more-info-executive" href="'+ link +'">Подробнее</a>\n' +
                            '                    </div>\n' +
                            '                </div>';
                        break;

                    case 'text-date':
                        currentTemplate = '<div class="review-block">\n' +
                            '                    <p class="date-published">'+ postDate +'</p>\n' +
                            '                    <p class="title-executive">'+ title +'</p>\n' +
                            '                    <div class="review-content">'+ content +'</div>\n' +
                            '                </div>';
                        break;

                    case 'text':
                        currentTemplate = '<div class="review-block">\n' +
                            '                    <p class="title-executive">'+ title +'</p>\n' +
                            '                    <div class="review-content">'+ content +'</div>\n' +
                            '                </div>';
                        break;
                    default:
                        currentTemplate = '<div class="review-block">\n' +
                            '                    <p class="title-executive">'+ title +'</p>\n' +
                            '                    <div class="review-content">'+ content +'</div>\n' +
                            '                </div>';
                }


                let html = currentTemplate;

                container.append(html);
            }

        },
        error: function (response) {
            console.log(response);
        }
    })
}