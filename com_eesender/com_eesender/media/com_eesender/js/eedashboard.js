function ViewTest() { };
ViewTest.prototype = {

    //show this screen
    show: function () {
       console.log('test');
    }
};

//register with ee
ee.views["test"] = new ViewTest(); 