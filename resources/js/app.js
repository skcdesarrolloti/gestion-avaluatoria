import Alpine from 'alpinejs';
import { appraisalForm } from './appraisal-form.js';
import { installFetchNavigation } from './fetch-navigation.js';
import { installModuleAutosave } from './module-autosave.js';
import { photoUpload } from './photo-upload.js';
import { sectorBankTabs } from './sector-bank-tabs.js';
import { subjectAttributes } from './subject-attributes.js';
import { installUploadProgress } from './upload-progress.js';

window.Alpine = Alpine;
Alpine.data('appraisalForm', appraisalForm);
Alpine.data('photoUpload', photoUpload);
Alpine.data('sectorBankTabs', sectorBankTabs);
Alpine.data('subjectAttributes', subjectAttributes);
Alpine.start();
installModuleAutosave();
installUploadProgress();
installFetchNavigation();
