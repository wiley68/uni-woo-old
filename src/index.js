/** WC blocks payment method */
import { registerPaymentMethod } from '@woocommerce/blocks-registry';

const settings_uni = window.wc.wcSettings.getSetting( 'uni_payment_gateway_data', {} );
const label_uni = window.wp.htmlEntities.decodeEntities( settings_uni.title ) || 'УНИ Кредит покупки на Кредит';
let uniParva = 0;
let uniRequest = false;
let uniPendingCalculations = 0;

const UniPaymentMethod = () => {
    const setUniCalculationNotice = (visible) => {
        const notice = document.getElementById('uni_calculation_notice');
        if (notice) {
            notice.style.display = visible ? 'block' : 'none';
        }
    };

    const setUniConfirmationDisabled = (disabled) => {
        const selectors = [
            '#place_order',
            'button[name="woocommerce_checkout_place_order"]',
            'button[type="submit"]'
        ];
        selectors.forEach((selector) => {
            document.querySelectorAll(selector).forEach((button) => {
                button.disabled = disabled;
            });
        });
    };
    
    const uniPostAjax = (url, data, success) => {
        var params = typeof data == 'string' ? data : Object.keys(data).map(
            function(k){ return encodeURIComponent(k) + '=' + encodeURIComponent(data[k]) }
        ).join('&');
        var xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
        let isCompleted = false;
        const finalize = () => {
            if (isCompleted) {
                return;
            }
            isCompleted = true;
            uniPendingCalculations = Math.max(0, uniPendingCalculations - 1);
            if (uniPendingCalculations === 0) {
                setUniConfirmationDisabled(false);
                setUniCalculationNotice(false);
            }
        };
        xhr.open('POST', url);
        xhr.onreadystatechange = function() {
            if (xhr.readyState>3 && xhr.status==200) { success(xhr.responseText); }
            if (xhr.readyState === 4) {
                finalize();
            }
        };
        xhr.onerror = function() {
            console.error('Request failed: ', xhr.statusText);
            finalize();
        };
        xhr.onabort = finalize;
        xhr.ontimeout = finalize;
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.send(params);
        return xhr;
    }
    
    const calculateUni = (_uni_meseci, _uni_parva, _uni_description, _uni_egn, _uni_phone2, _uni_uslovia_check) => {
        let params = '&action=unipayment_checkout';
        params += '&uni_promo=' + settings_uni.uni_promo;
        params += '&uni_promo_data=' + settings_uni.uni_promo_data;
        params += '&uni_promo_meseci_znak=' + settings_uni.uni_promo_meseci_znak;
        params += '&uni_promo_meseci=' + settings_uni.uni_promo_meseci;
        params += '&uni_promo_price=' + settings_uni.uni_promo_price;
        params += '&uni_product_cat_id=' + settings_uni.uni_product_cat_id;
        params += '&uni_meseci=' + _uni_meseci;
        params += '&uni_total_price=' + settings_uni.uni_total;
        params += '&uni_parva=' + _uni_parva;
        params += '&uni_eur=' + settings_uni.uni_eur;
        params += '&uni_description=' + _uni_description;
        params += '&uni_egn=' + _uni_egn;
        params += '&uni_phone2=' + _uni_phone2;
        params += '&uni_uslovia_check=' + _uni_uslovia_check;
        params += '&uni_proces2=' + settings_uni.uni_proces2;
        
        uniPendingCalculations += 1;
        setUniConfirmationDisabled(true);
        setUniCalculationNotice(true);
        uniPostAjax(unipayment_checkout.ajax_url, params, (data) => {
            const json = JSON.parse(data);
            document.getElementById("uni_pogasitelni_vnoski").value = _uni_meseci;
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
        });
    }
    
    let uniTitleText;
    if (settings_uni.uni_proces1 == 1) {
        uniTitleText = "Можете да изберете 'Срок за кредита', предпочитаната от Вас 'Месечна вноска', както и при желание 'Първоначална вноска'. След което да потвърдите избора си. Ще бъдете прехвърлени към страницата на UNI Credit за довършване на покупката си на кредит."
    } else {
        uniTitleText = "Можете да изберете 'Срок за кредита', предпочитаната от Вас 'Месечна вноска', както и при желание 'Първоначална вноска'. Въвеждате необходимите лични данни. Съгласявате се с условията за използването им. След което можете да потвърдите избора си. Сътрудник от UNI Credit ще се свърже с Вас за завършване на процедурата."
    }
    
    document.addEventListener('click', (event) => {
        if (uniPendingCalculations > 0 && event.target.id === 'place_order') {
            event.preventDefault();
            event.stopPropagation();
            return false;
        }
        if (event.target.id == "radio-control-wc-payment-method-options-uni_payment_gateway") {
            if (event.target.checked == true) {
                calculateUni(settings_uni.uni_shema_current, uniParva, '', '', '', 0);
            }
        }
    });

    document.addEventListener('submit', (event) => {
        if (uniPendingCalculations > 0) {
            event.preventDefault();
            event.stopPropagation();
            return false;
        }
    });
    
    let uniPriceInfo;
    let uniTotal;
    let uniObshtoTxt;
    let uniMesecnaTxt;
    let uniObshtaSuma;
    if (settings_uni.uni_eur == 0 || settings_uni.uni_eur == 3) {
        uniPriceInfo = "Цена на продукта /" + settings_uni.uni_sign + "/";
        uniTotal = settings_uni.uni_total;
        uniObshtoTxt = "Общ размер на кредита /" + settings_uni.uni_sign + "/";
        uniMesecnaTxt = "Месечна вноска /" + settings_uni.uni_sign + "/";
        uniObshtaSuma = "Обща дължима сума /" + settings_uni.uni_sign + "/";
    } else {
        uniPriceInfo = "Цена на продукта /" + settings_uni.uni_sign + "(" + settings_uni.uni_sign_second + ")/";
        uniTotal = settings_uni.uni_total + " (" + settings_uni.uni_price_second + ")";
        uniObshtoTxt = "Общ размер на кредита /" + settings_uni.uni_sign + "(" + settings_uni.uni_sign_second + ")/";
        uniMesecnaTxt = "Месечна вноска /" + settings_uni.uni_sign + "(" + settings_uni.uni_sign_second + ")/";
        uniObshtaSuma = "Обща дължима сума /" + settings_uni.uni_sign + "(" + settings_uni.uni_sign_second + ")/";
    }
    
    const uniPogasitelniVnoskiChange = (event) => {
        const _uniParva = document.getElementById("uni_parva").value;
        const _uniDescription = document.getElementById("uni_description") ? document.getElementById("uni_description").value : '';
        const _uniEgn = document.getElementById("uni_egn") ? document.getElementById("uni_egn").value : '';
        const _uniPhone2 = document.getElementById("uni_phone2") ? document.getElementById("uni_phone2").value : '';
        const _uniUsloviaCheck = document.getElementById("uni_uslovia_check") ? document.getElementById("uni_uslovia_check").checked ? 1 : 0 : 0;
        calculateUni(event.target.value, _uniParva, _uniDescription, _uniEgn, _uniPhone2, _uniUsloviaCheck);
    }
    
    const uniParvaChec = (event) => {
        if (event.target.checked == true) {
            document.getElementById("uni_parva").readOnly = false;
        }else{
            document.getElementById("uni_parva").readOnly = true;
        }
    }
    
    const uniUsloviaCheck = (event) => {
        const _uniVnoski = document.getElementById("uni_pogasitelni_vnoski").value;
        const _uniParva = document.getElementById("uni_parva").value;
        const _uniDescription = document.getElementById("uni_description") ? document.getElementById("uni_description").value : '';
        const _uniEgn = document.getElementById("uni_egn") ? document.getElementById("uni_egn").value : '';
        const _uniPhone2 = document.getElementById("uni_phone2") ? document.getElementById("uni_phone2").value : '';
        if (event.target.checked == true) {
            calculateUni(_uniVnoski, _uniParva, _uniDescription, _uniEgn, _uniPhone2, 1);
        }else{
            calculateUni(_uniVnoski, _uniParva, _uniDescription, _uniEgn, _uniPhone2, 0);
        }
    }
    
    const uniInputChange = (event) => {
        const _uniVnoski = document.getElementById("uni_pogasitelni_vnoski").value;
        const _uniParva = document.getElementById("uni_parva").value;
        const _uniDescription = document.getElementById("uni_description") ? document.getElementById("uni_description").value : '';
        const _uniEgn = event.target.value;
        const _uniPhone2 = document.getElementById("uni_phone2") ? document.getElementById("uni_phone2").value : '';
        const _uniUsloviaCheck = document.getElementById("uni_uslovia_check") ? document.getElementById("uni_uslovia_check").checked ? 1 : 0 : 0;
        calculateUni(_uniVnoski, _uniParva, _uniDescription, _uniEgn, _uniPhone2, _uniUsloviaCheck);
    }
    
    const uniParvaButton = () => {
        const _uniVnoski = document.getElementById("uni_pogasitelni_vnoski").value;
        const _uniParva = document.getElementById("uni_parva").value;
        const _uniDescription = document.getElementById("uni_description") ? document.getElementById("uni_description").value : '';
        const _uniEgn = document.getElementById("uni_egn") ? document.getElementById("uni_egn").value : '';
        const _uniPhone2 = document.getElementById("uni_phone2") ? document.getElementById("uni_phone2").value : '';
        const _uniUsloviaCheck = document.getElementById("uni_uslovia_check") ? document.getElementById("uni_uslovia_check").checked ? 1 : 0 : 0;
        calculateUni(_uniVnoski, _uniParva, _uniDescription, _uniEgn, _uniPhone2, _uniUsloviaCheck);
    }
  
    return (
        <div id="uni_box">
            <div className="uni_title">
                {uniTitleText}
            </div>
            <div
                id="uni_calculation_notice"
                style={{
                    color: '#993300',
                    fontSize: '13px',
                    marginTop: '8px',
                    display: 'none'
                }}
            >
                Преизчисляване... моля, изчакайте.
            </div>
            <div style={{"padding-bottom": "5px"}}></div>
            <table className="uni_table">
                <tr>
                    <td className="uni_row_title">
                        {uniPriceInfo}
                    </td>
                    <td className="uni_row_input">
                        <input 
                            type="text" 
                            className="uni_input passive" 
                            readonly="readonly" 
                            value={uniTotal}
                        />
                    </td>
                </tr>
                <tr>
                    <td className="uni_row_title">
                        Срок на кредита /месеца/
                    </td>
                    <td className="uni_row_input">
                        <select 
                            name="uni_pogasitelni_vnoski" 
                            id="uni_pogasitelni_vnoski" 
                            className="uni_input"
                            onChange={uniPogasitelniVnoskiChange}
                        >
                            {settings_uni.uni_meseci_3 == 1 && <option value="3">3 месеца</option>}
                            {settings_uni.uni_meseci_4 == 1 && <option value="4">4 месеца</option>}
                            {settings_uni.uni_meseci_5 == 1 && <option value="5">5 месеца</option>}
                            {settings_uni.uni_meseci_6 == 1 && <option value="6">6 месеца</option>}
                            {settings_uni.uni_meseci_9 == 1 && <option value="9">9 месеца</option>}
                            {settings_uni.uni_meseci_10 == 1 && <option value="10">10 месеца</option>}
                            {settings_uni.uni_meseci_12 == 1 && <option value="12">12 месеца</option>}
                            {settings_uni.uni_meseci_15 == 1 && <option value="15">15 месеца</option>}
                            {settings_uni.uni_meseci_18 == 1 && <option value="18">18 месеца</option>}
                            {settings_uni.uni_meseci_24 == 1 && <option value="24">24 месеца</option>}
                            {settings_uni.uni_meseci_30 == 1 && <option value="30">30 месеца</option>}
                            {settings_uni.uni_meseci_36 == 1 && <option value="36">36 месеца</option>}
                        </select>
                    </td>
                </tr>
                {settings_uni.uni_first_vnoska == "Yes" && 
                <>
                <tr>
                    <td className="uni_row_title">
                        <table 
                            style={{
                                width: '100%',
                                padding: '0px',
                                margin: '0px'
                            }}>
                            <tr>
                                <td className="uni_row">
                                    <input 
                                        type="checkbox" 
                                        id="uni_parva_chec" 
                                        title="Ако искате да използвате полето за Първоначална Вноска, моля отбележете тази отметка!"
                                        onClick={uniParvaChec}
                                    />
                                </td>
                                <td className="uni_row">
                                    Първоначална вноска към търговеца /{settings_uni.uni_sign}/
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td className="uni_row_input">
                        <input 
                            className="uni_input" 
                            type="text" 
                            readonly="readonly" 
                            name="uni_parva" 
                            id="uni_parva"
                        />
                    </td>
                </tr>
                <tr>
                    <td className="uni_row_title">&nbsp;</td>
                    <td className="uni_row_input">
                        <div 
                            type="button" 
                            className="uni_btn_pre" 
                            id="uni_parva_button"
                            onClick={uniParvaButton}
                        >Преизчисли</div>
                    </td>
                </tr>
                </>}
                {settings_uni.uni_first_vnoska != "Yes" &&
                <>
                    <input 
                        type="hidden" 
                        id="uni_parva_chec" 
                        value="0"
                    />
                    <input 
                        type="hidden" 
                        name="uni_parva" 
                        id="uni_parva"
                    />
                </>}
                <tr>
                    <td className="uni_row_title">
                        {uniObshtoTxt}
                    </td>
                    <td className="uni_row_input">
                        <input 
                            type="hidden" 
                            name="uni_obshto" 
                            id="uni_obshto"
                        />
                        <input 
                            className="uni_input passive" 
                            type="text" 
                            name="uni_obshto_second" 
                            id="uni_obshto_second" 
                            readonly="readonly"
                        />
                    </td>
                </tr>
                <tr>
                    <td className="uni_row_title">
                        {uniMesecnaTxt}
                    </td>
                    <td className="uni_row_input">
                        <input 
                            type="hidden" 
                            name="uni_mesecna" 
                            id="uni_mesecna"
                        />
                        <input 
                            className="uni_input passive" 
                            type="text" 
                            name="uni_mesecna_second" 
                            id="uni_mesecna_second" 
                            readonly="readonly"
                        />
                    </td>
                </tr>
                <tr>
                    <td className="uni_row_title">
                        {uniObshtaSuma}
                    </td>
                    <td className="uni_row_input">
                        <input 
                            type="hidden" 
                            name="uni_obshtozaplashtane" 
                            id="uni_obshtozaplashtane"
                        />
                        <input 
                            className="uni_input passive" 
                            type="text" 
                            name="uni_obshtozaplashtane_second" 
                            id="uni_obshtozaplashtane_second" 
                            readonly="readonly"
                        />
                    </td>
                </tr>
                <tr>
                    <td className="uni_row_title">
                        ГЛП /%/
                    </td>
                    <td className="uni_row_input">
                        <input 
                            className="uni_input passive" 
                            type="text" 
                            name="uni_glp" 
                            id="uni_glp" 
                            readonly="readonly" 
                        />
                    </td>
                </tr>
                <tr>
                    <td className="uni_row_title">
                        ГПР /%/
                    </td>
                    <td className="uni_row_input">
                        <input 
                            className="uni_input passive" 
                            type="text" 
                            name="uni_gpr" 
                            id="uni_gpr" 
                            readonly="readonly" 
                        />
                    </td>
                </tr>
            </table>
            {settings_uni.uni_proces2 == 1 &&
            <>
                <div className="uni_hr">&nbsp;</div>
                <table className="uni_table">
                    <tr>
                        <td className="uni_row_title">
                            Име
                        </td>
                        <td className="uni_row_input">
                            <input 
                                id="uni_fname" 
                                name="uni_fname" 
                                required 
                                type="text" 
                                className="uni_input" 
                                value={settings_uni.uni_firstname}
                            />
                        </td>
                    </tr>
                    <tr>
                        <td className="uni_row_title">
                            Фамилия
                        </td>
                        <td className="uni_row_input">
                            <input 
                                id="uni_lname" 
                                name="uni_lname" 
                                required 
                                type="text" 
                                className="uni_input" 
                                value={settings_uni.uni_lastname}
                            />
                        </td>
                    </tr>
                    <tr>
                        <td className="uni_row_title">
                            ЕГН
                        </td>
                        <td className="uni_row_input">
                            <input 
                                id="uni_egn" 
                                name="uni_egn" 
                                required 
                                type="text" 
                                className="uni_input"
                                onChange={uniInputChange}
                            />
                        </td>
                    </tr>
                    <tr>
                        <td className="uni_row_title">
                            Телефон
                        </td>
                        <td className="uni_row_input">
                            <input 
                                id="uni_phone" 
                                name="uni_phone" 
                                required 
                                type="text" 
                                className="uni_input" 
                                value={settings_uni.uni_phone}
                            />
                        </td>
                    </tr>
                    <tr>
                        <td className="uni_row_title">
                            Допълнителен Телефон
                        </td>
                        <td className="uni_row_input">
                            <input 
                                id="uni_phone2" 
                                name="uni_phone2" 
                                required 
                                type="text" 
                                className="uni_input"
                            />
                        </td>
                    </tr>
                    <tr>
                        <td className="uni_row_title">
                            E-Mail
                        </td>
                        <td className="uni_row_input">
                            <input 
                                id="uni_email" 
                                name="uni_email" 
                                required 
                                type="text" 
                                className="uni_input" 
                                value={settings_uni.uni_email}
                            />
                        </td>
                    </tr>
                    <tr>
                        <td className="uni_row_title">
                            Коментар
                        </td>
                        <td className="uni_row_input">
                            <textarea 
                                id="uni_description" 
                                name="uni_description" 
                                className="uni_input"
                            ></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div style={{"display":"flex"}}>
                                <input 
                                    type="checkbox" 
                                    name="uni_uslovia_check" 
                                    value="uni_uslovia" 
                                    id="uni_uslovia_check" 
                                    onClick={uniUsloviaCheck}
                                />
                                <a 
                                    href={settings_uni.uni_uslovia} 
                                    style={{"color": "#993300"}} 
                                    title="Общи условия за UniCredit лизинг." 
                                    target="_blank"
                                >&nbsp;Прочетох и съм съгласен с Общите условия на UniCredit</a>
                            </div>
                        </td>
                    </tr>
                </table>
            </>}
            <div className="uni_hr">&nbsp;</div>
            <div className="uni_text_cc">C.C.Ver. {settings_uni.uni_mod_version}</div>
        </div>
    );
};

registerPaymentMethod({
    name: 'uni_payment_gateway',
    label: label_uni,
    ariaLabel: label_uni,
    content: <UniPaymentMethod />,
    edit: <UniPaymentMethod />,
    canMakePayment: () => true,
});
/** WC blocks payment method */
