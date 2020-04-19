define([], function() {
    var select = $('#countriesList'),
        btn = $('#searchBtn'),
        countryTax = $('#countryTaxId'),
        avgTaxRate = $('#avgTaxRateId'),
        avgAmountPerState = $('#avgAmountPerStateId'),
        statisticsPerState = $('#statisticsPerStateId'),
        uploadCsvBtn = $('#csvFileUploadId'),
        uploadExcelBtn = $('#excelFileUploadId');


    uploadCsvBtn.on('change', function() {
        uploadCsvBtn.closest('form').submit();
    });

    uploadExcelBtn.on('change', function () {
        uploadExcelBtn.closest('form').submit();
    });

    btn.on('click', getStatistics);
    (new Promise(function (resolve) {
        $.get('/list/countries', null, function (response) {
            resolve(response);
        });
    })).then(function(response) {
        if (!response) {
            return false;
        }

        response.forEach(function(data) {
            select.append(`<option value="${data.code}">${data.name}</option>`);
        });

        if (response.length) {
            select.val(response[0].code);
            select.trigger('change');
            btn.removeAttr("disabled").removeClass('disabled');
        }
    });

    function getStatistics() {
        if (!select.val()) {
            return;
        }

        (new Promise(function (resolve) {
            $.get('/statistic/' + select.val(), null, function (response) {
                countryTax.text(response.country_tax || 0);
                avgTaxRate.text(response.avg_tax_rate || 0);
                avgAmountPerState.text(response.avg_amount_per_state || 0);
                resolve(response);
            });
        })).then(function(response) {
            var table = statisticsPerState.find('tbody');

            table.empty();

            if (!response || !response.stats) {
                return;
            }

            response.stats.forEach(function(data) {
                table.append(`<tr data-id="${data.id}"><td>${data.name}</td><td>${data.total}</td><td>${data.average}</td></tr>`);
            });
        });
    }
});