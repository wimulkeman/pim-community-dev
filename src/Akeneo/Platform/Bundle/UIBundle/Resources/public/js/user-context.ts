const routing = require('routing');
const Backbone = require('backbone');
const _ = require('underscore');

type UserContextData = {
  uiLocale: string | null;
  catalogLocale: string | null;
  catalogScope: string | null;
  ui_locale_decimal_separator: string | null;
  code: string | null;
  enabled: boolean;
  username: string | null;
  email: string | null;
  name_prefix: string | null;
  first_name: string | null;
  middle_name: null;
  last_name: string | null;
  name_suffix: string | null;
  phone: string | null;
  image: string | null;
  last_login: number;
  login_count: number;
  catalog_default_locale: string | null;
  user_default_locale: string | null;
  catalog_default_scope: string | null;
  default_category_tree: string | null;
  email_notifications: boolean;
  timezone: string | null;
  groups: string[];
  roles: string[];
  product_grid_filters: [];
  avatar: {filePath: string | null; originalFilename: string | null};
  meta: {id: number; created: number; updated: number; form: string; image: {filePath: string | null}};
  properties: [];
};

class Events {
  constructor() {
    _.extend(this, Backbone.Events);
  }

  on(eventName: string, callback?: Function, context?: any): any {
    return;
  }
  off(eventName?: string, callback?: Function, context?: any): any {
    return;
  }
  trigger(eventName: string, ...args: any[]): any {
    return;
  }
  bind(eventName: string, callback: Function, context?: any): any {
    return;
  }
  unbind(eventName?: string, callback?: Function, context?: any): any {
    return;
  }

  once(events: string, callback: Function, context?: any): any {
    return;
  }
  listenTo(object: any, events: string, callback: Function): any {
    return;
  }
  listenToOnce(object: any, events: string, callback: Function): any {
    return;
  }
  stopListening(object?: any, events?: string, callback?: Function): any {
    return;
  }
}

class UserContext extends Events {
  private data: UserContextData | null = null;

  constructor() {
    super();
  }

  initialize() {
    return fetch(routing.generate('pim_user_user_rest_get_current')).then(async response => {
      this.data = await response.json();

      if (null === this.data) {
        throw new Error('');
      }

      this.data.uiLocale = this.data.user_default_locale;
      this.data.catalogLocale = this.data.catalog_default_locale;
      this.data.catalogScope = this.data.catalog_default_scope;
    });
  }

  get(key: keyof UserContextData) {
    if (null === this.data) throw new Error('You cannog call userContext.get before calling userContext.initialize()');

    return this.data[key];
  }

  set(key: 'catalogScope' | 'catalogLocale', value: string, options?: {silent: boolean}) {
    if (null === this.data) throw new Error('You cannog call userContext.get before calling userContext.initialize()');

    this.data[key] = value;

    if (!options?.silent) this.trigger(`change:${key}`);
  }
}
const userContext = new UserContext();

module.exports = userContext;
