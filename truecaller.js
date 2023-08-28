#!/usr/bin/env node
var http = require('http')
var url = require('url')
const truecallerjs = require('truecallerjs')
http.createServer(function (req, res) {
    res.writeHead(200, { 'Content-Type': 'Application/json' })
    /*Use the url module to turn the querystring into an object:*/
    var q = url.parse(req.url, true).query
    /*Return the year and month from the query object:*/
    // var txt = q.year + " " + q.month;
    var number = q.number
    var searchData = {
        number: number,
        countryCode: 'IN',
        // installationId: "KRoPr32d61ec91eac4e57907103bd87c69187",
        installationId: 'a2i0s--ahYKTwF-VV5fIyBO4MKyG0CvX2wpbgk7fk7zW0jxiUSJgHQnCO6Vzjmd-',
        output: 'JSON'
    }
    var sn = truecallerjs.searchNumber(searchData)
    sn.then(function (response) {
        // console.log(response)
        res.end(response)
    })
    // res.end(response);
})
    .listen(8081)
