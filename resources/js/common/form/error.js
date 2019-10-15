import _ from 'lodash'
import { isEmpty, inProduction } from '@common/utils'

export default class Error {
  /**
   * Create a new Errors instance.
   */
  constructor () {
    this.original = null
    this.message = null
    this.code = null
    this.errors = {}
  }

  /**
   * Determine if an errors exists for the given field.
   *
   * @param {string} field
   */
  has (field) {
    return this.errors.hasOwnProperty(field)
  }

  /**
   * Determine if an errors exists for the given fields.
   *
   * @param {array} fields
   */
  contains (fields) {
    for (let field in fields) {
      if (this.has(fields[field])) {
        return true
      }
    }

    return false
  }

  /**
   * Determine if has error message.
   */
  hasMessage () {
    return this.message !== null
  }

  /**
   * Determine if has error has been set.
   */
  hasSet () {
    return this.original !== null
  }

  /**
   * Determine if the error is internal error.
   */
  isInternalError () {
    return typeof _.get(this.original, 'response') === 'undefined'
  }

  /**
   * Determine if the error is validation error.
   */
  isValidationError () {
    return this.code === 422
  }

  /**
   * Determine if we have any errors.
   */
  any () {
    return Object.keys(this.errors).length > 0
  }

  /**
   * Retrieve the error message for a field.
   *
   * @param {string} field
   */
  get (field) {
    if (!field) {
      return this.errors
    }

    if (this.errors[field]) {
      return _.get(this.errors, [field, 0])
    }
  }

  /**
   * Record the new errors.
   *
   * @param {object} errors
   */
  record (errors) {
    this.errors = Object(errors)
  }

  /**
   * Set the error message.
   *
   * @param {string} message
   */
  setMessage (message) {
    if (inProduction() && this.isInternalError()) {
      message = ''
    }

    this.message = isEmpty(message) ? 'Terdapat kesalahan pada server!' : message
  }

  /**
   * Set the original error.
   *
   * @param {object} error
   */
  setError (error) {
    this.original = error

    const response = _.get(error, 'response.data')

    if (isEmpty(response)) {
      this.setMessage(_.get(error, 'message'))
      this.record([''])
    } else {
      this.setMessage(_.get(response, 'message'))
      this.record(_.get(response, 'errors'))
    }

    this.code = _.get(error, 'response.status')
  }

  /**
   * Clear one or all error fields.
   *
   * @param {string|null} field
   */
  clear (field = null) {
    if (field) {
      delete this.errors[field]

      return
    }

    this.original = null
    this.message = null
    this.code = null
    this.errors = {}
  }
}
