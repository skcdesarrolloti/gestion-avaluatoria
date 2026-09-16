import Alpine from 'alpinejs';
import { appraisalForm } from './appraisal-form.js';
import { installFetchNavigation } from './fetch-navigation.js';
import { photoUpload } from './photo-upload.js';

window.Alpine = Alpine;
Alpine.data('appraisalForm', appraisalForm);
Alpine.data('photoUpload', photoUpload);
Alpine.start();
installFetchNavigation();
