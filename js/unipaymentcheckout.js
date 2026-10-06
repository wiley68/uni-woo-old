let uniPendingCalculations = 0;
let uniCalculationReady = false;
let uniCalculationRequest = 0;
let uniCalculatedPrice = null;
let uniCalculatedMonths = null;
let uniCalculatedBox = null;
let uniPreferredMonths = null;

function isUniMethodSelected() {
    const method = document.getElementById('payment_method_uni_payment_gateway');
    return !!document.getElementById('uni-checkout-container') && (!method || method.checked);
}

function setUniConfirmationDisabled(disabled) {
    const selectors = [
        '#place_order',
        'button[name="woocommerce_checkout_place_order"]',
        'button[type="submit"]'
    ];
    selectors.forEach((selector) => {
        document.querySelectorAll(selector).forEach((button) => {
            button.disabled = isUniMethodSelected() && disabled;
        });
    });
}

function setUniCalculationNotice(visible, failed) {
    const noticeId = 'uni_calculation_notice';
    let notice = document.getElementById(noticeId);
    if (!notice) {
        notice = document.createElement('div');
        notice.id = noticeId;
        notice.style.color = '#993300';
        notice.style.fontSize = '13px';
        notice.style.marginTop = '8px';
        notice.style.display = 'none';
        notice.textContent = 'Преизчисляване... моля, изчакайте.';
        const uniBox = document.getElementById('uni_box');
        if (uniBox) {
            uniBox.appendChild(notice);
        }
    }
    if (notice) {
        notice.textContent = failed
            ? 'Изчислението е неуспешно. Моля, изберете срока отново или натиснете Преизчисли.'
            : 'Преизчисляване... моля, изчакайте.';
        notice.style.display = visible ? 'block' : 'none';
    }
}

function uniPostAjax(url, data, success, complete) {
    var params = typeof data == 'string' ? data : Object.keys(data).map(
            function(k){ return encodeURIComponent(k) + '=' + encodeURIComponent(data[k]) }
        ).join('&');
    var xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
    var isCompleted = false;
    var finalize = function() {
        if (isCompleted) {
            return;
        }
        isCompleted = true;
        if (typeof complete === 'function') {
            complete();
        }
    };
    xhr.open('POST', url);
    xhr.onreadystatechange = function() {
        if (xhr.readyState > 3 && xhr.status == 200) {
            try {
                success(xhr.responseText);
            } finally {
                finalize();
            }
        }
        if (xhr.readyState === 4) {
            finalize();
        }
    };
    xhr.onerror = finalize;
    xhr.onabort = finalize;
    xhr.ontimeout = finalize;
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.send(params);
    return xhr;
}
function calculateUni(_uni_meseci){
    const box = document.getElementById('uni-checkout-container');
    const price = document.getElementById('uni_price');
    if (!box || !price) {
        return;
    }
    const request = ++uniCalculationRequest;
    const requestedPrice = price.value;
    uniCalculationReady = false;
    document.getElementById('uni_calculated_price').value = '';
    let params = '&action=unipayment_checkout';
    params += '&uni_promo=' + document.getElementById("uni_promo").value;
    params += '&uni_promo_data=' + document.getElementById("uni_promo_data").value;
    params += '&uni_promo_meseci_znak=' + document.getElementById("uni_promo_meseci_znak").value;
    params += '&uni_promo_meseci=' + document.getElementById("uni_promo_meseci").value;
    params += '&uni_promo_price=' + document.getElementById("uni_promo_price").value;
    params += '&uni_product_cat_id=' + document.getElementById("uni_product_cat_id").value;
    params += '&uni_meseci=' + _uni_meseci;
    params += '&uni_total_price=' + requestedPrice;
    params += '&uni_parva=' + document.getElementById("uni_parva").value;
    params += '&uni_eur=' + document.getElementById("uni_eur").value;
    
    uniPendingCalculations = 1;
    setUniConfirmationDisabled(true);
    setUniCalculationNotice(true);
    uniPostAjax(unipayment_checkout.ajax_url, params, (data) => {
        if (request !== uniCalculationRequest || box !== document.getElementById('uni-checkout-container') ||
            requestedPrice !== document.getElementById('uni_price').value ||
            String(_uni_meseci) !== document.getElementById('uni_pogasitelni_vnoski').value) {
            return;
        }
        let json;
        try {
            json = JSON.parse(data);
        } catch (error) {
            return;
        }
        if (json.success !== 'success' || !Number.isFinite(Number(json.uni_mesecna)) || Number(json.uni_mesecna) <= 0) {
            return;
        }
        document.getElementById("uni_obshto").value = json.uni_obshto;
        if (json.uni_obshto_second == 0) {
            document.getElementById("uni_obshto_second").value = json.uni_obshto;
        }else{
            document.getElementById("uni_obshto_second").value = json.uni_obshto + " (" + json.uni_obshto_second + ")";
        }
        document.getElementById("uni_mesecna").value = json.uni_mesecna;
        if (json.uni_mesecna_second == 0) {
            document.getElementById("uni_mesecna_second").value = json.uni_mesecna;
        }else{
            document.getElementById("uni_mesecna_second").value = json.uni_mesecna + " (" + json.uni_mesecna_second + ")";
        }
        document.getElementById("uni_obshtozaplashtane").value = json.uni_obshtozaplashtane;
        if (json.uni_obshtozaplashtane_second == 0) {
            document.getElementById("uni_obshtozaplashtane_second").value = json.uni_obshtozaplashtane;
        }else{
            document.getElementById("uni_obshtozaplashtane_second").value = json.uni_obshtozaplashtane + " (" + json.uni_obshtozaplashtane_second + ")";
        }
        document.getElementById("uni_glp").value = json.uni_glp;
        document.getElementById("uni_gpr").value = json.uni_gpr;
        document.getElementById("uni_kop").value = json.uni_kop;
        document.getElementById('uni_calculated_price').value = requestedPrice;
        uniCalculatedPrice = requestedPrice;
        uniCalculatedMonths = String(_uni_meseci);
        uniCalculatedBox = box;
        uniCalculationReady = true;
    }, () => {
        if (request === uniCalculationRequest) {
            uniPendingCalculations = 0;
            setUniConfirmationDisabled(!uniCalculationReady);
            setUniCalculationNotice(!uniCalculationReady, !uniCalculationReady);
        }
    });
}
function refreshUniCheckoutCalculation() {
    const price = document.getElementById('uni_price');
    const months = document.getElementById('uni_pogasitelni_vnoski');
    const box = document.getElementById('uni-checkout-container');
    if (months && uniPreferredMonths !== null) {
        if (Array.from(months.options).some((option) => option.value === uniPreferredMonths)) {
            months.value = uniPreferredMonths;
        } else {
            uniPreferredMonths = null;
        }
    }
    if (price && months && box &&
        (!uniCalculationReady || price.value !== uniCalculatedPrice || months.value !== uniCalculatedMonths || box !== uniCalculatedBox)) {
        calculateUni(months.value);
    } else if (price && months && box) {
        setUniConfirmationDisabled(false);
    }
}
document.addEventListener('DOMContentLoaded', () => {
    refreshUniCheckoutCalculation();
    jQuery(document.body).on('update_checkout', () => {
        if (document.getElementById('uni-checkout-container')) {
            ++uniCalculationRequest;
            uniCalculationReady = false;
            uniPendingCalculations = 0;
            document.getElementById('uni_calculated_price').value = '';
            setUniConfirmationDisabled(true);
            setUniCalculationNotice(true);
        }
    });
    jQuery(document.body).on('updated_checkout', refreshUniCheckoutCalculation);

    document.addEventListener('submit', (event) => {
        if (isUniMethodSelected() &&
            (!uniCalculationReady || uniPendingCalculations > 0)) {
            event.preventDefault();
            event.stopPropagation();
            return false;
        }
    });

    document.addEventListener('click', (event) => {
        if (
            isUniMethodSelected() &&
            (!uniCalculationReady || uniPendingCalculations > 0) &&
            (
                event.target.id == "place_order" ||
                event.target.name == "woocommerce_checkout_place_order"
            )
        ){
            event.preventDefault();
            event.stopPropagation();
            return false;
        }
        if (event.target.id == "uni_parva_chec"){
            if (event.target.checked == true){
                document.getElementById("uni_parva").readOnly = false;
            }else{
                document.getElementById("uni_parva").readOnly = true;
            }
        }
        if (event.target.id == "uni_parva_button"){
            calculateUni(document.getElementById("uni_pogasitelni_vnoski").value);
        }
        if (event.target.id == "uni_uslovia_check"){
            if (event.target.checked == true){
                document.getElementById("uni_saglasie").value = "Yes";
            }else{
                document.getElementById("uni_saglasie").value = "No";
            }
        }
    });
    document.addEventListener('change', (event) => {
        if (event.target.name === 'payment_method') {
            setUniConfirmationDisabled(!uniCalculationReady || uniPendingCalculations > 0);
        }
        if (event.target.id == "uni_pogasitelni_vnoski"){
            uniPreferredMonths = event.target.value;
            calculateUni(event.target.value);
        }
    });
});
