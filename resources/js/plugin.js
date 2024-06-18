(function ()
{
    this.Traverise = function ()
    {
        let defaults = {};

        this.elements = [];
        this.settings = (arguments[0] && typeof arguments[0] === 'object') ? extendDefaults(defaults, arguments[0]) : defaults;

        this.init();
    }

    Traverise.prototype.init = function ()
    {
        console.log('Init plugin.');

        build.call(this);
    }

    Traverise.prototype.build = function (element)
    {
        console.log('Update plugin.')
    }

    function build (element)
    {
        console.log('Build plugin.')
    }

    function extendDefaults (defaults, properties)
    {
        Object.keys(properties).forEach(property => {
            if (properties.hasOwnProperty(property)) {
                defaults[property] = properties[property];
            }
        });

        return defaults;
    }
})();
