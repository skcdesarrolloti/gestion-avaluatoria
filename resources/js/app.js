import Alpine from 'alpinejs';
import { appraisalForm } from './appraisal-form.js';
import { installFetchNavigation } from './fetch-navigation.js';
import { installModuleAutosave } from './module-autosave.js';
import { photoUpload } from './photo-upload.js';
import { sectorBankTabs } from './sector-bank-tabs.js';

window.Alpine = Alpine;
Alpine.data('appraisalForm', appraisalForm);
Alpine.data('photoUpload', photoUpload);
Alpine.data('sectorBankTabs', sectorBankTabs);
Alpine.start();
installModuleAutosave();
installFetchNavigation();
