import _ from 'lodash'
import Error from './error'
import { isEmpty } from '@common/utils'

export default class Form {
  /**
   * Create a new Form instance.
   *
   * @param {object} data
   * @param {object} context
   * @param {string|null} key
   * @param {int} alertInterval
   */
  constructor (data, context = null, key = 'form', alertInterval = 10) {
    this.isLoading = false
    this.disableButton = false
    this.originalData = _.cloneDeep(data)
    this.alertInterval = alertInterval
    this.showSuccessAlert = false
    this.successMessage = null
    this.showErrorAlert = false

    for (let field in data) {
      this[field] = data[field]
    }

    this.error = new Error()

    if (context) {
      this.watch(context, key)
    }
  }

  /**
   * Fetch all relevant data for the form.
   */
  data () {
    let data = {}

    for (let property in this.originalData) {
      data[property] = this[property]
    }

    return data
  }

  /**
   * Fetch all fields from the form.
   */
  fields (except = null) {
    let fields = []

    for (let property in this.originalData) {
      fields.push(property)
    }

    return fields.filter((field) => !_.castArray(except).includes(field))
  }

  /**
   * Watch all fields in form.
   *
   * @param {object} context
   * @param {string} key
   * @param {Function|null} callback
   */
  watch (context, key = 'form', callback = null) {
    context.$nextTick(() => {
      context.$watch((vm) => {
        const watching = [Date.now()]

        for (let field in this.originalData) {
          watching.push(vm[key][field])
        }

        return watching.join()
      }, val => {
        this.disableButton = false
        if (typeof callback === 'function') {
          callback(val)
        }
      })
    })
  }

  /**
   * Reset the form fields.
   *
   * @param {boolean} resetError
   * @param {array} except
   * @param {boolean} shouldEmpty
   * @param {boolean} errorOnly
   */
  reset (resetError = true, except = [], shouldEmpty = false, errorOnly = false) {
    if (!errorOnly) {
      for (let field in this.originalData) {
        if (!_.includes(except, field)) {
          this[field] = shouldEmpty ? '' : this.originalData[field]
        }
      }
    }

    if (resetError) {
      this.error.clear()
    }
  }

  /**
   * Submit the form.
   *
   * @param {Function} callback
   * @param {boolean} throwError
   */
  async submit (callback, throwError = true) {
    this.showSuccessAlert = false
    this.showErrorAlert = false
    this.isLoading = true
    this.disableButton = true
    this.error.clear()

    let data

    try {
      data = await callback()
      this.onSuccess(data)
    } catch (error) {
      this.onFail(error)

      if (throwError) {
        throw error
      }
    }

    return data
  }

  /**
   * Handle a successful form submission.
   *
   * @param {object} data
   */
  onSuccess (data) {
    this.isLoading = false
    this.disableButton = false
    this.showSuccessAlert = true
    this.successMessage = _.get(data, 'data.message')
  }

  /**
   * Handle a failed form submission.
   *
   * @param {object} error
   */
  onFail (error) {
    this.isLoading = false
    this.disableButton = true
    this.error.setError(error)

    const response = _.get(error, 'response.data')

    if (isEmpty(response)) {
      this.error.setMessage(_.get(error, 'message'))
      this.error.record([''])
    } else {
      this.error.setMessage(_.get(response, 'message'))
      this.error.record(_.get(response, 'errors'))
    }

    const errorCode = _.get(this.error, 'original.response.status')

    this.showErrorAlert = errorCode !== 422 && this.error.hasMessage()
  }
}
