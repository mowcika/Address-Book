// function to act as a class
function OptionsComponent() {
}

// gets called once before the renderer is used
OptionsComponent.prototype.init = function (params) {
    // create the cell
    this.gridApi = document.createElement('div')
    this.gridApi.innerHTML = '<span class="my-css-class"><button class="btn-simple">Delete</button><span class="my-value"></span></span>'

    // get references to the elements we want
    this.eButton = this.gridApi.querySelector('.btn-simple')
    this.eValue = this.gridApi.querySelector('.my-value')

    // set value into cell
    this.eValue.innerHTML = params.valueFormatted ? params.valueFormatted : params.value

    // add event listener to button
    this.eventListener = function () {
        params.frameworkComponentWrapper._parent.$parent.$parent.destroy(params)
    }
    this.eButton.addEventListener('click', this.eventListener)
}

// gets called once when grid ready to insert the element
OptionsComponent.prototype.getGui = function () {
    return this.gridApi
}

// gets called whenever the user gets the cell to refresh
OptionsComponent.prototype.refresh = function (params) {
    // set value into cell again
    this.eValue.innerHTML = params.valueFormatted ? params.valueFormatted : params.value
    // return true to tell the grid we refreshed successfully
    return true
}

// gets called when the cell is removed from the grid
OptionsComponent.prototype.destroy = function () {
    // do cleanup, remove event listener from button
    this.eButton.removeEventListener('click', this.eventListener)
}

export default OptionsComponent
