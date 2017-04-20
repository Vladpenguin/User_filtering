$(document).ready(function () {
    init();

    edication = [];
    city = [];
    timer = 2000;
    var timeout;

    // sdsda
    $('#filter-edu').change(function () {
        clearTimeout(timeout);
        edication = $(this).val();
        timeout = setTimeout(send, timer);
    });

    $('#filter-city').change(function(){
        clearTimeout(timeout);
        city = $(this).val();
        timeout = setTimeout(send, timer);
    });
});

/**
 * Функция для
 */
function send() {
    $.ajax({
        url: "/libs/request.php",
        method: "POST",
        dataType: "json",
        data: {
            education: edication,
            city: city
        },
        success: function (resp) {
            fill_table(resp.users_cities);
        }
    });
}

/**
 * Функция для инициализации
 */
function init() {
    $.ajax({
        url: "/libs/request.php",
        method: "POST",
        dataType: "json",
        success: function (resp) {
            var educations = [];
            var cities = [];
            $(resp.educations).each(function(){
                 educations.push('<option value="'+ this.id +'">'+ this.name +'</option>');
            });
            $(resp.cities).each(function(){
                cities.push('<option value="'+ this.id +'">'+ this.name +'</option>');
            });
            $('#filter-edu').html(educations);
            $('#filter-city').html(cities);

            fill_table(resp.users_cities);

        }
    });
}

/**
 * Функция построения таблицы ответа
 * @param rows
 */
function fill_table(rows) {
    var users_cities = [];

    $(rows).each(function(){
        users_cities.push('<tr><td>'+ this.name_user +'</td><td>'+ this.name_edu +'</td><td>'+ this.name_city +'</td></tr>');
    });

    $('#table_body').html(users_cities);
}