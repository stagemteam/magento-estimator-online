let indexImg = 0;
let indexImgBrand = 0;
let dataForm = new VarienForm('estimator-form');

jQuery(document).ready(function($) {
    $("#phone").mask('+1 999 999 9999');
    if (categories && categories.length) {
        initCategories();
    }
    initAddons();
    if (selected) {
        fillFields();
        fillAddonsFields();
        fillCustomerFields();
    }
    // power info close outside
    $(window).click(function (e) {
        try {
            let className = e.target.className;
            let parentClass = e.target.offsetParent.offsetParent.className;
            if (className !== 'info-icon' && parentClass !== 'powerInfo' && className !== 'powerInfo') {
                jQuery('.powerInfo').hide();
            }
        } catch (e) {
            jQuery('.powerInfo').hide();
        }
    });
});

function fillFields() {
    if (selected && selected.category_id) {
        jQuery('input:radio[name="category"]').filter('[value="' + selected.category_id + '"]').click();
    }
    if (selected && selected.manufacturer_id) {
        jQuery('input:radio[name="manufacturer"]').filter('[value="' + selected.manufacturer_id + '"]').click();
    }
    if (selected && selected.configurable_id) {
        jQuery('input:radio[name="configurable"]').filter('[value="' + selected.configurable_id + '"]').click();
    }
    if (selected && selected.product_id) {
        jQuery('input:radio[name="product"]').filter('[value="' + selected.product_id + '"]').click();
    }
}

function fillCustomerFields() {
    Object.keys(selected.customer).forEach(function (val) {
        let input = jQuery('input[name="customer[' + val + ']"]');
        if (input) {
            input.val(selected.customer[val]);
        }
    });
}
function fillAddonsFields () {
    Object.keys(selected.addons).forEach(function (val) {
        jQuery('input:radio[name="addons[' + val + ']"]').filter('[value="' + selected.addons[val] + '"]').click();
        jQuery('input:checkbox[name="addons[' + val + ']"]').filter('[value="' + selected.addons[val] + '"]').click();
        jQuery('input[type="text"][name="addons[' + val + ']"]').val(selected.addons[val]);
        jQuery('input[type="number"][name="addons[' + val + ']"]').val(selected.addons[val]);
        jQuery('textarea[name="addons[' + val + ']"]').val(selected.addons[val]);
    });
}

function initCategories() {
    let categoriesContainer = jQuery('#categories-container');
    categories.forEach(function (category, index) {
        let categoryElement = jQuery('<div class="input-box">'
            + '<input id="category-' + category.id + '"'
            + ' type="radio"'
            + ' data-category-id="' + category.id + '"'
            + ' value="' + category.value + '"'
            + ' name="category"/>'
            + '<label for="category-' + category.id + '">' + category.label + '</label>'
            + '</div>'
        );
        categoryElement.find('input').change(function () {
            hiddenElement('#submit-form-button');
            initImages('#estimator-category-image-box', category.image, index, indexImg);
            //initImages('#estimator-category-image-box2', category.image, index, indexImg);
            indexImg = index;
            jQuery('#estimator-brand-image-box').empty();
            jQuery('#estimator-brand-image-box2').empty();
            jQuery('#estimator-brand-general-image-box').empty();
            toggleSteps(2, 6, false);
            initBrands(jQuery(this).data('categoryId'));
        });
        categoriesContainer.append(categoryElement);
        showElement('#generalStep');
    });
}
function initBrands(categoryId) {
    const brandsContainer = jQuery("#brands-container");
    brandsContainer.empty();
    if (brands && brands.length) {
        let filteredBrands = brands.filter(function (brand) { return  brand.categoryIds.indexOf(categoryId) !== -1 });
        let logosContainer = jQuery('#estimator-brand-general-image-box');
        filteredBrands.forEach(function (brand, index) {
            let brandElement = jQuery('<div class="input-box"</div>').append(jQuery(
                '<input id="brand-' + brand.id + '"'
                + ' type="radio"'
                + ' data-brand-id="' + brand.id + '"'
                + ' value="' + brand.value + '"'
                + ' name="manufacturer"/>'
                + '<label for="brand-' + brand.id + '">' + brand.label + '</label>'
            ));
            if (brand.logo) {
                logosContainer.append('<div class="col-xs-6"><div class="estimator-brand-logo" style="background-image: url(' + brand.logo + ')"></div></div>');
            }
            brandElement.find('input').change(function() {
                hiddenElement('#submit-form-button');
                initImages('#estimator-brand-image-box', brand.generalImage, index, indexImgBrand);
                initImages('#estimator-brand-image-box2', brand.generalImage, index, indexImgBrand);
                indexImgBrand = index;
                toggleSteps(3,6,false);
                initModels(categoryId, jQuery(this).data('brandId'));
            });
            brandsContainer.append(brandElement)
        });
        showElement('#step1');
    }
}
function initModels(categoryId, brandId) {
    const modelsContainer = jQuery("#models-container");
    modelsContainer.empty();
    if (models && models.length) {
        let filteredModels = models.filter(function (model) { return model.categoryId == categoryId && model.brandId == brandId });
        filteredModels.forEach(function (model) {
            let modelElement = jQuery('<div class="input-box">'
                + '<input id="model-'+model.id+'"'
                + ' type="radio"'
                + ' data-model-id="'+model.id+'"'
                + ' value="'+model.value+'"'
                + ' name="configurable"/>'
                + '<label for="model-'+model.id+'">'+model.label+'</label>'
                + '<a href="' + BASE_URL + '/quickview/index/view/id/' + model.id + '" class="quickview-icon"><i class="icon-export"></i><span>' + Translator.translate('Quick View') + '</span></a>'
                + '</div>');
            modelElement.find('input').change(function () {
                toggleSteps(4,6,false);
                initPowers(jQuery(this).data('modelId'));
            });
            modelsContainer.append(modelElement)
        });
        showElement('#step2');
    }
}

function initPowers(modelId) {
    const powersContainer = jQuery("#powers-container");
    powersContainer.empty();
    if (powers && powers.length) {
        let filteredPowers = powers.filter(function (power) {
            return Number(power.modelId) === Number(modelId)
        });
        filteredPowers.forEach(function (power) {
            let powerElement = jQuery('<div class="input-box">' +
                '<input id="power-' + power.id + '"'
                + ' type="radio"'
                + ' value="' + power.value + '"'
                + ' name="product"/>' +
                '<label for="power-' + power.id + '">' + power.label + '</label></div>'
            );
            powerElement.find('input').change(function () {
                toggleSteps(4, 6, true);
                showElement('#submit-form-button');
            });
            powersContainer.append(powerElement)
        });
        showElement('#step3');
    }
}
function initAddons () {
    let separatedAddonsContainer = jQuery('#separated-addons-container');
    let additionalRequirementsContainer = jQuery('#additionalRequirements-container');
    additionalRequirementsContainer.empty();
    separatedAddonsContainer.empty();
    if (addons && addons.separated && addons.separated.length) {
        addons.separated./*sort(function (a, b) {return a.order - b.order}).*/forEach(function (option) {
            let addonElement = jQuery('<div class="card"><div class="card-header">' +
                '<span>' + option.label + '</span></div>' +
                '<div class="card-body">' +
                '<div class="separatedContainer"></div></div></div>');
            let addonPropsContainer = addonElement.find('.separatedContainer');
            if (option.type === 'number' || option.type === 'text') {
                renderAddonProps(addonPropsContainer, generateInput(option, false), option.message);
            } else if (option.type === 'textarea') {
                renderAddonProps(addonPropsContainer, generateTextarea(option), option.message);
            } else {
                if (Array.isArray(option.value)) {
                    option.value.forEach(function (val) {
                        let elements = generateCheckboxOrRadio({
                            label: val.label,
                            value: val.value,
                            name: option.name,
                            title: option.title,
                            type: option.type
                        });
                        renderAddonProps(addonPropsContainer, elements, option.message);
                    });
                } else {
                    renderAddonProps(addonPropsContainer, generateCheckboxOrRadio(option), option.message);
                }
            }
            separatedAddonsContainer.append(addonElement);
        });
    }
    if (addons && addons.grouped && addons.grouped.length) {
        addons.grouped./*sort(function (a, b) {return a.order - b.order}).*/forEach(function (option) {
            if (option.type === 'number' || option.type === 'text') {
                renderAddonPropsWithContainer(additionalRequirementsContainer, generateInput(option, true), option.message);
            } else if (option.type === 'textarea') {
                let addon = jQuery('<div class="addon"></div>');
                let elements = generateTextarea(option);
                addon.append(elements.label);
                renderAddonProps(addon, elements, option.message);
                additionalRequirementsContainer.append(addon);
            } else if (option.type === 'date') {
                renderAddonPropsWithContainer(additionalRequirementsContainer, generateDateInput(option, true), option.message);
            } else if (option.type === 'checkbox' || option.type === 'radio') {
                if (Array.isArray(option.value)) {
                    let addon = jQuery('<div class="addon"></div>');
                    addon.append('<div class="input-box"><label class="strong">' + option.label + '</label></div>');
                    option.value.forEach(function (val) {
                        let elements = generateCheckboxOrRadio({
                            label: val.label,
                            value: val.value,
                            name: option.name,
                            title: option.title,
                            type: option.type,
                            class: 'fw-normal'
                        });
                        addon.append(elements.element);
                    });
                    additionalRequirementsContainer.append(addon);
                } else {
                    renderAddonPropsWithContainer(additionalRequirementsContainer, generateCheckboxOrRadio(option), option.message);
                }
            }
        });
    }
}

function generateCheckboxOrRadio(option) {
    let element = jQuery('<div class="input-box">'
        + '<input id="' + option.name + option.value + '" '
        + 'title="' + option.title + '" '
        + 'type="' + option.type + '" '
        + 'value="' + option.value + '" '
        + 'name="' + option.name + '"/>'
        + '<label class="' + option.class + '" for="' + option.name + option.value + '">' + option.label + '</label>'
        + '</div>');
    let result = {
        element: element,
        message: ''
    };
    if (option.infoMessage && option.infoMessage !== '') {
        result.message = jQuery('<span class="small">'+option.infoMessage+'</span>');
    }
    return result;
}
function generateDateInput(option, showLabel) {
    let element = jQuery('<div class="input-box input">'
        + '<input id="' + option.name + '-' + option.id + '" '
        + 'title="' + option.title + '" '
        + 'class="input-text" '
        + 'type="text" '
        + 'autocomplete="off" '
        + 'name="' + option.name + '" '
        + 'placeholder="' + option.placeholder + '" '
        + 'value="" '
        + '/></div>');
    if (showLabel) {
        element.prepend(jQuery('<label for="' + option.name + '-' + option.id + '">' + option.label + '</label>'));
    }
    element.find('input').first().datetimepicker({step: 30});
    let result = {
        element: element,
        message: ''
    };
    if (option.infoMessage && option.infoMessage !== '') {
        result.message = jQuery('<span class="small">' + option.infoMessage + '</span>');
    }
    return result;
}
function generateInput(option, showLabel) {
    let element = jQuery('<div class="input-box input">'
        + '<input id="' + option.name + '-' + option.id + '" '
        + 'title="' + option.title + '" '
        + 'class="input-text" '
        + 'type="' + option.type + '" '
        + 'name="' + option.name + '" '
        + 'placeholder="' + option.placeholder + '" '
        + 'value="" '
        + '/></div>');
    if (showLabel) {
        element.prepend(jQuery('<label for="' + option.name + '-' + option.id + '">' + option.label + '</label>'));
    }
    let result = {
        element: element,
        message: ''
    };
    if (option.infoMessage && option.infoMessage !== '') {
        result.message = jQuery('<span class="small">' + option.infoMessage + '</span>');
    }
    return result;
}

function generateTextarea(option) {
    let label = jQuery('<label for="' + option.name + '-' + option.id + '">' + option.label + '</label>');
    let element = jQuery('<div class="input-box">' +
        '<textarea class="input-text textarea"'
        + ' id="' + option.name + '-' + option.id + '"'
        + ' name="' + option.name + '"'
        + ' rows="2"'
        + ' placeholder="' + option.placeholder + '"'
        + ' title="' + option.title + '"'
        + '></textarea></div>'
    );
    let result = {
        label: label,
        element: element,
        message: ''
    };
    if (option.infoMessage && option.infoMessage !== '') {
        result.message = jQuery('<span class="small">' + option.infoMessage + '</span>');
    }
    return result;
}

function showElement(selector) {
    jQuery(selector).show(500, function () {});
}
function hiddenElement(selector) {
    jQuery(selector).hide(500, function () {});
}
function toggleSteps(from, to, show) {
    for (let i = from; i <= to; i++) {
        (show) ? showElement('#step'+i) : hiddenElement('#step'+i);
    }
}
function powerInfoToggle() {
    jQuery('.powerInfo').toggle();
}

function initImages(selector, image, index, lastImgIndex) {
    let container = jQuery(selector);
    container.empty();
    if (image) {
        container.append('<div class="col-xs-12"><div class="estimator-category-image" style="background-image: url(' + image + '); display: none;"></div></div>');
        showImage(selector, index, lastImgIndex);
    }
}
function renderAddonProps(container, element, showMessage) {
    container.append(element.element);
    if (showMessage) {
        container.append(element.message);
    }
}
function renderAddonPropsWithContainer(container, element, showMessage) {
    let addon = jQuery('<div class="addon"></div>');
    addon.append(element.element);
    if (showMessage) {
        addon.append(element.message);
    }
    container.append(addon);
}
function showImage(container, index, prevIndex) {
    let direction = 'up';
    if (prevIndex > index) {
        direction = 'down'
    }
    // @todo: Needs update jQueryUI
    //jQuery(container).find('div').show('slide', {direction: direction}, 1000);
    jQuery(container).find('div').show('slide');
}
