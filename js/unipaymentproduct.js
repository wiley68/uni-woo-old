    let uni_old_vnoski;

    function uniConvertToDotDecimal(price) {
        price = price.trim();
        if (price.includes('.') && price.includes(',')) {
            if (price.lastIndexOf(',') < price.lastIndexOf('.')) {
                price = price.replace(/,/g, '');
            } else {
                price = price.replace(/\./g, '').replace(/,/g, '.');
            }
        } else if (price.includes(',')) {
            if (price.split(',').length - 1 === 1) {
                price = price.replace(/,/g, '.');
            } else {
                price = price.replace(/,/g, '');
            }
        }
        return price;
    }

    jQuery(document).ready(function($) {
        const uni_price = document.getElementById('uni_price');
        if (uni_price != null) {
            $("#uni-product-popup-container").prependTo("body");
            const uni_pogasitelni_vnoski_input = document.getElementById("uni_pogasitelni_vnoski_input");
            const btn_uni = document.getElementById("btn_uni");
            const uni_cart = document.getElementById("uni_cart") !== null ? document.getElementById("uni_cart").value : "";
            const uniProductPopupContainer = document.getElementById("uni-product-popup-container");
            const uni_back_unicredit = document.getElementById("uni_back_unicredit");
            const uni_buy_unicredit = document.getElementById("uni_buy_unicredit");
            const uni_buy_buttons_submit = document.querySelectorAll('button[type="submit"].single_add_to_cart_button');
            let uni_price1 = uni_price.value;
            let uni_quantity = 1;
            let uni_priceall = parseFloat(uni_price1) * uni_quantity;

            if (uni_pogasitelni_vnoski_input !== null && btn_uni !== null) {
                
                function uni_pogasitelni_vnoski_input_change() {
                    const uni_vnoski = parseFloat(document.getElementById("uni_pogasitelni_vnoski_input").value);
                    const uni_price = parseFloat(document.getElementById('uni_price').value);
                    const uni_param_kimb_3 = parseFloat(document.getElementById("uni_param_kimb_3").value);
                    const uni_param_kimb_4 = parseFloat(document.getElementById("uni_param_kimb_4").value);
                    const uni_param_kimb_5 = parseFloat(document.getElementById("uni_param_kimb_5").value);
                    const uni_param_kimb_6 = parseFloat(document.getElementById("uni_param_kimb_6").value);
                    const uni_param_kimb_9 = parseFloat(document.getElementById("uni_param_kimb_9").value);
                    const uni_param_kimb_10 = parseFloat(document.getElementById("uni_param_kimb_10").value);
                    const uni_param_kimb_12 = parseFloat(document.getElementById("uni_param_kimb_12").value);
                    const uni_param_kimb_18 = parseFloat(document.getElementById("uni_param_kimb_18").value);
                    const uni_param_kimb_24 = parseFloat(document.getElementById("uni_param_kimb_24").value);
                    const uni_param_kimb_30 = parseFloat(document.getElementById("uni_param_kimb_30").value);
                    const uni_param_kimb_36 = parseFloat(document.getElementById("uni_param_kimb_36").value);

                    var data = {
                        "action": "unipayment_product",
                        "uni_vnoski": uni_vnoski,
                        "uni_price": uni_price,
                        "uni_param_kimb_3": uni_param_kimb_3,
                        "uni_param_kimb_4": uni_param_kimb_4,
                        "uni_param_kimb_5": uni_param_kimb_5,
                        "uni_param_kimb_6": uni_param_kimb_6,
                        "uni_param_kimb_9": uni_param_kimb_9,
                        "uni_param_kimb_10": uni_param_kimb_10,
                        "uni_param_kimb_12": uni_param_kimb_12,
                        "uni_param_kimb_18": uni_param_kimb_18,
                        "uni_param_kimb_24": uni_param_kimb_24,
                        "uni_param_kimb_30": uni_param_kimb_30,
                        "uni_param_kimb_36": uni_param_kimb_36
                    };
                    
                    $.post(unipayment_product.ajaxurl, data, function(response) {
                        const uni_eur = parseInt(document.getElementById("uni_eur").value);
                        let uni_mesecna = 0;
                        let uni_glp = 0;
                        let uni_gpr = 0;
                        const json = JSON.parse(response);
                        switch (uni_vnoski) {
                            case 3:
                                uni_mesecna = parseFloat(json.uni_mesecna_3);
                                uni_gpr = parseFloat(json.uni_gpr_3);
                                uni_glp = parseFloat(document.getElementById("uni_param_glp_3").value);
                            break;
                            case 4:
                                uni_mesecna = parseFloat(json.uni_mesecna_4);
                                uni_gpr = parseFloat(json.uni_gpr_4);
                                uni_glp = parseFloat(document.getElementById("uni_param_glp_4").value);
                            break;
                            case 5:
                                uni_mesecna = parseFloat(json.uni_mesecna_5);
                                uni_gpr = parseFloat(json.uni_gpr_5);
                                uni_glp = parseFloat(document.getElementById("uni_param_glp_5").value);
                            break;
                            case 6:
                                uni_mesecna = parseFloat(json.uni_mesecna_6);
                                uni_gpr = parseFloat(json.uni_gpr_6);
                                uni_glp = parseFloat(document.getElementById("uni_param_glp_6").value);
                            break;
                            case 9:
                                uni_mesecna = parseFloat(json.uni_mesecna_9);
                                uni_gpr = parseFloat(json.uni_gpr_9);
                                uni_glp = parseFloat(document.getElementById("uni_param_glp_9").value);
                            break;
                            case 10:
                                uni_mesecna = parseFloat(json.uni_mesecna_10);
                                uni_gpr = parseFloat(json.uni_gpr_10);
                                uni_glp = parseFloat(document.getElementById("uni_param_glp_10").value);
                            break;
                            case 12:
                                uni_mesecna = parseFloat(json.uni_mesecna_12);
                                uni_gpr = parseFloat(json.uni_gpr_12);
                                uni_glp = parseFloat(document.getElementById("uni_param_glp_12").value);
                            break;
                            case 18:
                                uni_mesecna = parseFloat(json.uni_mesecna_18);
                                uni_gpr = parseFloat(json.uni_gpr_18);
                                uni_glp = parseFloat(document.getElementById("uni_param_glp_18").value);
                            break;
                            case 24:
                                uni_mesecna = parseFloat(json.uni_mesecna_24);
                                uni_gpr = parseFloat(json.uni_gpr_24);
                                uni_glp = parseFloat(document.getElementById("uni_param_glp_24").value);
                            break;
                            case 30:
                                uni_mesecna = parseFloat(json.uni_mesecna_30);
                                uni_gpr = parseFloat(json.uni_gpr_30);
                                uni_glp = parseFloat(document.getElementById("uni_param_glp_30").value);
                            break;
                            case 36:
                                uni_mesecna = parseFloat(json.uni_mesecna_36);
                                uni_gpr = parseFloat(json.uni_gpr_36);
                                uni_glp = parseFloat(document.getElementById("uni_param_glp_36").value);
                            break;
                            default:
                                uni_mesecna = parseFloat(json.uni_mesecna_3);
                                uni_gpr = parseFloat(json.uni_gpr_3);
                                uni_glp = parseFloat(document.getElementById("uni_param_glp_3").value);
                        }
                        const uni_vnoska_int = document.getElementById("uni_vnoska_int");
                        const uni_vnoska_dec = document.getElementById("uni_vnoska_dec");
                        const uni_vnoska_second_int = document.getElementById("uni_vnoska_second_int");
                        const uni_vnoska_second_dec = document.getElementById("uni_vnoska_second_dec");
                        const uni_glp_int = document.getElementById("uni_glp_int");
                        const uni_gpr_int = document.getElementById("uni_gpr_int");
                        const uni_vnoska_arr = uni_mesecna.toFixed(2).split(".");
                        uni_vnoska_int.textContent = uni_vnoska_arr[0];
                        uni_vnoska_dec.textContent = uni_vnoska_arr[1];
                        if (uni_vnoska_second_int !== null && uni_vnoska_second_dec !== null) {
                            if (uni_eur == 1){
                                const uni_vnoska_second_arr = (uni_mesecna / 1.95583).toFixed(2).split(".");
                                uni_vnoska_second_int.textContent = uni_vnoska_second_arr[0];
                                uni_vnoska_second_dec.textContent = uni_vnoska_second_arr[1];
                            }
                            if (uni_eur == 2){
                                const uni_vnoska_second_arr = (uni_mesecna * 1.95583).toFixed(2).split(".");
                                uni_vnoska_second_int.textContent = uni_vnoska_second_arr[0];
                                uni_vnoska_second_dec.textContent = uni_vnoska_second_arr[1];
                            }
                        }
                        uni_glp_int.textContent = uni_glp.toFixed(2);
                        uni_gpr_int.textContent = uni_gpr.toFixed(2);
                    });
                }
                
                uni_pogasitelni_vnoski_input.addEventListener('change', event => {
                    uni_pogasitelni_vnoski_input_change();
                });
                
                uni_pogasitelni_vnoski_input.addEventListener('focus', event => {
                    uni_old_vnoski = $(this).val();
                });
                
                btn_uni.addEventListener('click', event => {
                    if (uni_cart == "on"){
                        if (uni_buy_buttons_submit.length){
                            uni_buy_buttons_submit.item(0).click();
                        }
                    }else{
                        //get price with options
                        var variationDiv = document.getElementsByClassName("woocommerce-variation-price");
                        if (typeof variationDiv[0] !== 'undefined'){
                            var variationSpan1 = variationDiv[0].getElementsByTagName("span");
                            if (typeof variationSpan1[0] !== 'undefined'){
                                var variationSpan2 = variationSpan1[0].getElementsByTagName("span");
                                if (typeof variationSpan2[0] !== 'undefined'){
                                    var tps = variationSpan2[0].innerHTML.split("&");
                                    uni_price1 = tps[0];
                                }
                                var variationIns = variationSpan1[0].getElementsByTagName("ins");
                                if (typeof variationIns[0] !== 'undefined'){
                                    var variationSpan3 = variationIns[0].getElementsByTagName("span");
                                    if (typeof variationSpan3[0] !== 'undefined'){
                                        var tps = variationSpan3[0].innerHTML.split("&");
                                        uni_price1 = tps[0];
                                    }
                                }
                            }
                        }
                        uni_price1 = uni_price1.replace(/[^\d.,]/g, '');
                        uni_price1 = uniConvertToDotDecimal(uni_price1);
                
                        if (document.getElementsByName("quantity") !== null){
                            uni_quantity = parseFloat(document.getElementsByName("quantity")[0].value);
                        }
                        uni_priceall = parseFloat(uni_price1) * uni_quantity;
                        
                        const uni_eur = parseInt(document.getElementById("uni_eur").value);
                        const uni_currency_code = document.getElementById("uni_currency_code").value;
                        switch (uni_eur) {
                            case 0:
                                break;
                            case 1:
                                if (uni_currency_code == "EUR") {
                                    uni_priceall = uni_priceall * 1.95583;
                                }
                                break;
                            case 2:
                            case 3:
                                if (uni_currency_code == "BGN") {
                                    uni_priceall = uni_priceall / 1.95583;
                                }
                                break;
                        }
                        
                        const uni_price = document.getElementById('uni_price');
                        uni_price.value = uni_priceall;
                        
                        const uni_price_int = document.getElementById('uni_price_int');
                        uni_price_int.innerHTML = Math.floor(uni_priceall);
                        const uni_price_dec = document.getElementById('uni_price_dec');
                        const decimalPartTwoDigitsStr = String(Math.ceil((uni_priceall - Math.trunc(uni_priceall)) * 100)).padStart(2, '0');
                        uni_price_dec.innerHTML = decimalPartTwoDigitsStr;
                        
                        const uni_price_second_int = document.getElementById("uni_price_second_int");
                        const uni_price_second_dec = document.getElementById("uni_price_second_dec");
                        if (uni_price_second_int !== null && uni_price_second_dec !== null) {
                            if (uni_eur == 1) {
                                const uni_price_second_arr = (uni_priceall / 1.95583).toFixed(2).split(".");
                                uni_price_second_int.textContent = uni_price_second_arr[0];
                                uni_price_second_dec.textContent = uni_price_second_arr[1];
                            }
                            if (uni_eur == 2) {
                                const uni_price_second_arr = (uni_priceall * 1.95583).toFixed(2).split(".");
                                uni_price_second_int.textContent = uni_price_second_arr[0];
                                uni_price_second_dec.textContent = uni_price_second_arr[1];
                            }
                        }
                        
                        uniProductPopupContainer.style.display = "block";
                        uni_pogasitelni_vnoski_input_change();
                    }
                });
                
                uni_back_unicredit.addEventListener('click', event => {
                    uniProductPopupContainer.style.display = "none";
                });
                
                uni_buy_unicredit.addEventListener('click', event => {
                    uniProductPopupContainer.style.display = "none";
                    if (uni_buy_buttons_submit.length){
                        uni_buy_buttons_submit.item(0).click();
                    }
                });
            }
        }
    });
    