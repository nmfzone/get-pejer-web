import Vue from 'vue'
import _ from 'lodash'
import moment from 'moment'


const baseApiUrl = `${process.env.MIX_API_URL}`

export const asset = (path) => {
  return joinUrl(App.baseUrl, path)
}

export const inProduction = () => process.env.NODE_ENV === 'production'

export const toBoolean = (value) => {
  if (value === 'true' || value === 1 || value === '1' || value === true) {
    return true
  }

  return false
}

export const isEmpty = (value) => {
  if (value instanceof File || value instanceof Date) {
    return false
  }

  if (_.isObject(value)) {
    return _.isEmpty(value)
  }

  return _.isNil(value) || (_.isString(value) && value.trim() === '')
}

export const isNotEmpty = (value) => {
  return !isEmpty(value)
}

export const getValue = (item, path, def = null) => {
  const val = _.get(item, path)

  return isEmpty(val) ? def : _.get(item, path)
}

export const arrayReplace = (data, search, replace) => {
  return _.map(data, (v, k) => {
    if (v.toLowerCase() === search.toLowerCase()) {
      return replace
    }

    return v
  })
}

/**
 * Get array only for the given key or include the given key.
 *
 * @param {array} data
 * @param {array|string} only
 * @param {boolean} partial
 * @return {array}
 */
export const arrayOnly = (data, only, partial = false) => {
  only = _.castArray(only)

  return _.reduce(data, (result, v, k) => {
    let remove = true

    only.forEach(val => {
      if (k === val || (partial && _.toLower(k).includes(_.toLower(val)))) {
        remove = false
        return
      }
    })

    if (!remove) {
      result[k] = v
    }

    return result
  }, {})
}

_.mixin({
  'getValue': getValue,
  'arrayReplace' : arrayReplace,
  'only': arrayOnly
})

export const isSet = (value) => {
  return typeof value !== 'undefined' && value !== null
}

export const isUnset = (value) => {
  return !isSet(value)
}

export const date = (value, format = null) => {
  return moment(value).format(format || 'DD/MM/YYYY h:mm')
}

export const joinUrl = (...args) => {
  let url = _.trim(_.map(args, (v) => _.trim(v, '/')).join('/'), '/')

  return _.toString(_.first(args)).startsWith('/') && url !== '/' ? `/${url}` : url
}

export const apiRoute = (url, replaces = null, params = null) => {
  return joinUrl(baseApiUrl, url)
}

export const buildParams = (params) => {
  let result = []

  const page = _.get(params, 'page', _.get(Vue.$route, 'query.page'))
  const perPage = _.get(params, 'perPage', _.get(Vue.$route, 'query.perPage'))
  const random = _.get(params, 'random')
  const includes = [].concat(_.get(params, 'includes', []))
  const sorts = [].concat(_.get(params, 'sorts', []))
  const filters = [].concat(_.get(params, 'filters', []))
  const exceptIds = [].concat(_.get(params, 'exceptIds', []))

  if (!isEmpty(page)) {
    result.push(`page=${page}`)
  }

  if (!isEmpty(perPage)) {
    result.push(`per_page=${perPage}`)
  }

  if (!isEmpty(random)) {
    result.push(`random=${random}`)
  }

  if (!isEmpty(includes)) {
    result.push(`include=${includes.join(',')}`)
  }

  if (!isEmpty(sorts)) {
    sorts.forEach((sort, i) => {
      sort = [].concat(sort)

      sort.forEach((s, j) => {
        result.push(`sort[${i}][${j}]=${s}`)
      })
    })
  }

  if (!isEmpty(filters)) {
    filters.forEach((filter, i) => {
      filter = [].concat(filter)

      filter.forEach((s, j) => {
        result.push(`filter[${i}][${j}]=${s}`)
      })
    })
  }

  if (!isEmpty(exceptIds)) {
    result.push(`exceptIds=${exceptIds.join(',')}`)
  }

  return result.join('&')
}

export const parseFilters = (filters) => {
  return _.filter(filters, (filter) => {
    const key = _.get(filter, 'key'),
      value = _.get(filter, 'value'),
      operator = _.get(filter, 'operator')

    return (Array.isArray(filter) && filter.length === 3) ||
      (!isEmpty(key) && !isEmpty(value) && !isEmpty(operator))
  })
}

export const getUrlParam = (name) => {
  const results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);

  if (results != null) {
    return results[1] || 0;
  }

  return null;
}

/**
 * Build url with the given params.
 *
 * @param {string} url
 * @param {object} replaces
 * @param {object} params
 * @return {string}
 */
export const route = (url, replaces = null, params = null) => {
  if (_.isObject(replaces)) {
    _.map(replaces, (v, k) => {
      url = _.replace(url, `:${k}`, v)
    })
  }

  return buildUrl(url, params)
}

export const buildUrl = (url, params = null) => {
  let result = url
  params = buildParams(params)

  if (params.length > 0) result += '?' + params

  return result
}

export const promise = (callback) => {
  return new Promise((resolve, reject) => {
    return typeof callback === 'function' ? callback(resolve, reject) : resolve(callback)
  })
}

export const getErrorData = (error) => {
  const data = _.get(error, 'response.data')

  if (isEmpty(data)) {
    return _.get(error, 'message')
  }

  return data
}

export const request = async (context, callback, resetOnError = true) => {
  context.commit('SET', {
    key: 'error',
    value: null
  })
  context.commit('SET', {
    key: 'isLoading',
    value: true
  })

  let data = {}

  try {
    data = await callback()

    context.commit('SET', {
      key: 'isLoading',
      value: false
    })
  } catch (error) {
    resetOnError && context.commit('RESET')

    context.commit('SET', {
      key: 'error',
      value: getErrorData(error)
    })

    throw error
  }

  return data
}

export const extractPagination = (data) => {
  return _.only(data, [
    'current_page',
    'first_page_url',
    'from',
    'last_page',
    'last_page_url',
    'next_page_url',
    'path',
    'per_page',
    'prev_page_url',
    'to',
    'total'
  ])
}

/**
 * Class merge helper.
 * NOTE: Please note that this helper produce unique result.
 *       So you should not used it in something like Semantic UI.
 *
 * @param {object|array|string} classes
 * @param {object|array|string} otherClasses
 * @param {array} unsets
 * @return object
 */
export const mergeClasses = (classes, otherClasses, unsets) => {
  const convertToObject = (classes) => {
    let newClasses = {}

    if (typeof classes === 'string') {
      classes = classes.split(' ')
      classes.forEach(v => {
        newClasses[v] = true
      })
    } else if (Array.isArray(classes)) {
      classes.forEach(v => {
        newClasses[v] = true
      })
    } else {
      newClasses = classes
    }

    return newClasses
  }

  classes = convertToObject(classes)
  otherClasses = convertToObject(otherClasses)

  const res = { ...classes, ...otherClasses }

  if (Array.isArray(unsets)) {
    unsets.forEach((v) => {
      _.unset(res, v)
    })
  }

  return res
}

export const createNamespacedConstants = (consts, prefix = '_') => {
  return _.transform(consts, (result, v1, k1) => {
    const prefixedValues = _.mapValues(v1, (v2, k2) => {
      if (_.isObject(v2)) {
        return _.mapValues(v2, (v3) => {
          return `${k1}/${k2}/${v3}`
        })
      }

      return `${k1}/${v2}`
    });

    const localKey = `${prefix}${k1}`;

    result[localKey] = v1;
    result[k1] = prefixedValues;
  })
}

export const vuexAssignDefaults = (moduleData) => {
  const initialState = moduleData.state()

  moduleData.mutations = {
    ...moduleData.mutations,
    ...{
      SET (state, payload) {
        Vue.set(state, payload.key, payload.value)
      },
      RESET (state, params) {
        let keys = _.get(params, 'keys')
        const only = _.get(params, 'only')

        const s = initialState

        if (only) {
          keys = _.castArray(keys)
        } else {
          keys = _.difference(Object.keys(s), keys)
        }

        keys.forEach(key => {
          if (state.hasOwnProperty(key)) {
            state[key] = s[key]
          }
        })
      }
    }
  }

  moduleData.actions = {
    ...moduleData.actions,
    ...{
      reset ({ commit }, params) {
        const keys = _.get(params, 'keys')
        const only = _.get(params, 'only')

        commit('RESET', { keys, only })
      }
    }
  }

  return moduleData
}
