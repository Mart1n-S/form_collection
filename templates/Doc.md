// Configuration des types de contrats
const contractConfigs = {
    CDI: {
        selector: 'input[data-contract="CDI"]',
        section: '#work_situation_CDI_CDD',
        extraInfo: '#more_info_situation',
        temporarySelector: 'input[data-contract="CDD"]',
        temporarySection: '#is_temporary_job'
    },
    "Professionnel ou agricole": {
        selector: 'input[data-contract="Professionnel ou agricole"]',
        section: '#work_situation_AGRI',
        extraInfo: '#more_info_situation'
    },
    "Autre": {
        selector: 'input[data-contract="Autre"]',
        section: '#work_situation_OTHER',
        extraInfo: '#more_info_situation'
    }
};

// Fonction générique
function toggleWorkSituation(config) {
    const isChecked = $(config.selector).is(':checked');

    if (isChecked) {
        $(config.section).removeClass('d-none');
        if (config.extraInfo) $(config.extraInfo).removeClass('d-none');

        // Cas particulier CDI/CDD → job temporaire
        if (config.temporarySelector) {
            if ($(config.temporarySelector).is(':checked')) {
                $(config.temporarySection).removeClass('d-none');
            } else {
                $(config.temporarySection).addClass('d-none');
                clearDiv($(config.temporarySection));
            }
        }

    } else {
        $(config.section).addClass('d-none');
        clearDiv($(config.section));
    }
}


// Listener principal
$("input[name='housing_project_form[customer][contractType]']").on("change", function () {
    $('#work_situation_details').removeClass('d-none');

    // Pour chaque configuration, on déclenche le traitement
    Object.values(contractConfigs).forEach(cfg => toggleWorkSituation(cfg));
});











######


// Configuration des types de contrats
const contractConfigs = {
    CDI_CDD: {
        selectors: ['input[data-contract="CDI"]', 'input[data-contract="CDD"]'],
        section: '#work_situation_CDI_CDD',
        showExtraInfo: true,
        temporarySelector: 'input[data-contract="CDD"]',     // Afficher job temporaire si CDD
        temporarySection: '#is_temporary_job'
    },
    AGRI: {
        selectors: ['input[data-contract="Professionnel ou agricole"]'],
        section: '#work_situation_AGRI',
        showExtraInfo: true
    },
    OTHER: {
        selectors: ['input[data-contract="Autre"]'],
        section: '#work_situation_OTHER',
        showExtraInfo: false // ← ICI : extra info doit être caché
    }
};


// Fonction générique
function toggleWorkSituation(config) {

    // Vérifie si au moins un des inputs associés est coché
    const isChecked = config.selectors.some(sel => $(sel).is(':checked'));

    if (isChecked) {
        $(config.section).removeClass('d-none');

        // Gérer l'affichage de more_info_situation
        if (config.showExtraInfo) {
            $('#more_info_situation').removeClass('d-none');
        } else {
            $('#more_info_situation').addClass('d-none');
        }

        // Cas particulier CDI/CDD → job temporaire
        if (config.temporarySelector) {
            if ($(config.temporarySelector).is(':checked')) {
                $(config.temporarySection).removeClass('d-none');
            } else {
                $(config.temporarySection).addClass('d-none');
                clearDiv($(config.temporarySection));
            }
        }

    } else {
        $(config.section).addClass('d-none');
        clearDiv($(config.section));
    }
}


// Listener principal
$("input[name='housing_project_form[customer][contractType]']").on("change", function () {
    $('#work_situation_details').removeClass('d-none');

    Object.values(contractConfigs).forEach(cfg => toggleWorkSituation(cfg));
});




