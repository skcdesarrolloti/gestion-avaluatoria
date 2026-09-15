import Alpine from 'alpinejs';
import { appraisalForm } from './appraisal-form.js';
import { installFetchNavigation } from './fetch-navigation.js';

window.Alpine = Alpine;
Alpine.data('appraisalForm', appraisalForm);
Alpine.start();
installFetchNavigation();
