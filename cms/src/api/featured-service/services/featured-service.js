'use strict';

/**
 * featured-service service
 */

const { createCoreService } = require('@strapi/strapi').factories;

module.exports = createCoreService('api::featured-service.featured-service');
