var $j = jQuery.noConflict();
$j(document).on("click","#uni_btn_create_kop",function(e) {
    e.preventDefault();
    let rows_id = document.getElementsByName('uni_category_id');
    let rows_kop = document.getElementsByName('uni_category_kop');
    let rows_promo = document.getElementsByName('uni_category_promo');
    let rows_kimb = document.getElementsByName('uni_category_kimb');
    let rows_kimb_time = document.getElementsByName('uni_category_kimb_time');
    let rows_kimb_3 = document.getElementsByName('uni_category_kimb_3');
    let rows_glp_3 = document.getElementsByName('uni_category_glp_3');
    let rows_kimb_4 = document.getElementsByName('uni_category_kimb_4');
    let rows_glp_4 = document.getElementsByName('uni_category_glp_4');
    let rows_kimb_5 = document.getElementsByName('uni_category_kimb_5');
    let rows_glp_5 = document.getElementsByName('uni_category_glp_5');
    let rows_kimb_6 = document.getElementsByName('uni_category_kimb_6');
    let rows_glp_6 = document.getElementsByName('uni_category_glp_6');
    let rows_kimb_9 = document.getElementsByName('uni_category_kimb_9');
    let rows_glp_9 = document.getElementsByName('uni_category_glp_9');
    let rows_kimb_10 = document.getElementsByName('uni_category_kimb_10');
    let rows_glp_10 = document.getElementsByName('uni_category_glp_10');
    let rows_kimb_12 = document.getElementsByName('uni_category_kimb_12');
    let rows_glp_12 = document.getElementsByName('uni_category_glp_12');
    let rows_kimb_15 = document.getElementsByName('uni_category_kimb_15');
    let rows_glp_15 = document.getElementsByName('uni_category_glp_15');
    let rows_kimb_18 = document.getElementsByName('uni_category_kimb_18');
    let rows_glp_18 = document.getElementsByName('uni_category_glp_18');
    let rows_kimb_24 = document.getElementsByName('uni_category_kimb_24');
    let rows_glp_24 = document.getElementsByName('uni_category_glp_24');
    let rows_kimb_30 = document.getElementsByName('uni_category_kimb_30');
    let rows_glp_30 = document.getElementsByName('uni_category_glp_30');
    let rows_kimb_36 = document.getElementsByName('uni_category_kimb_36');
    let rows_glp_36 = document.getElementsByName('uni_category_glp_36');
    let uni_categories = new Array(rows_id.length);
    for(let i=0; i<rows_id.length; i++){
        uni_categories[i] = {
            'category_id':parseInt(rows_id[i].innerHTML),
            'kop':rows_kop[i].value,
            'promo':rows_promo[i].value,
            'kimb':rows_kimb[i].value,
            'kimb_time':rows_kimb_time[i].value,
            'stats': {
                'kimb_3': rows_kimb_3[i].value,
                'glp_3': rows_glp_3[i].value,
                'kimb_4': rows_kimb_4[i].value,
                'glp_4': rows_glp_4[i].value,
                'kimb_5': rows_kimb_5[i].value,
                'glp_5': rows_glp_5[i].value,
                'kimb_6': rows_kimb_6[i].value,
                'glp_6': rows_glp_6[i].value,
                'kimb_9': rows_kimb_9[i].value,
                'glp_9': rows_glp_9[i].value,
                'kimb_10': rows_kimb_10[i].value,
                'glp_10': rows_glp_10[i].value,
                'kimb_12': rows_kimb_12[i].value,
                'glp_12': rows_glp_12[i].value,
                'kimb_15': rows_kimb_15[i].value,
                'glp_15': rows_glp_15[i].value,
                'kimb_18': rows_kimb_18[i].value,
                'glp_18': rows_glp_18[i].value,
                'kimb_24': rows_kimb_24[i].value,
                'glp_24': rows_glp_24[i].value,
                'kimb_30': rows_kimb_30[i].value,
                'glp_30': rows_glp_30[i].value,
                'kimb_36': rows_kimb_36[i].value,
                'glp_36': rows_glp_36[i].value
            }
        };
    }
    $j.ajax({
        url : unipayment_admin.ajax_url,
        type : 'post',
        dataType: 'json',
        data : {
            action : 'unipayment_kop',
            uni_categories_kop : uni_categories
        },
        success : function( json ) {
            if (json['success'] == 'success'){
                alert("Успешно записахте промените в таблицата със съответствия на Категории и КОП на SmartUCF UNI Credit системата.");
            }
        }
    });
});