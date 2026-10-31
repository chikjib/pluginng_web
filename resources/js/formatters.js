import moment from 'moment';
import numeral from 'numeral';

export const formatters = {
    formatDateTime: value => value ? moment(String(value)).format('DD/MM/YYYY hh:mm') : '',
    formatDate: value => value ? moment(String(value)).format('DD/MM/YYYY') : '',
    formatNumber: value => numeral(value).format('0,0.00'),
    formatPrice: value => numeral(value).format('0,0'),
};
