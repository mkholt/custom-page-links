/** CustomPageLinks meta
 * Author: Morten Holt
 * Created: 2015-11-30
 * Since: 1.0
 * Depends: [ 'jquery' ]
 */

if (!Array.range)
{
	/**
	 * Get an array of numeric elements from start to end, both including
	 *
	 * @param start The start element
	 * @param end The end element (if &lt;=start, will equal start)
	 * @param step The number of steps to take between each number (default 1)
	 * @returns {Array} An array containing the integers from start to end
	 */
	Array.range = function(start, end, step) {
		// Make sure we don't get a negative length,
		// also increment by 1 so we get the interval [start ... end] and not the interval [start ... end[
		end = Math.max(start, end || start) + 1;
		step = step || 1;
		var length = Math.ceil((end - start) / step);

		// If the map function is defined, simply use that
		if (typeof Array.prototype.map !== undefined)
		{
			return Array.apply(null, new Array(length)).map(function(v,i) {
				return (i*step) + start;
			});
		}

		// If map is not defined (IE8), fallback to a simple for loop
		var arr = new Array(length);
		for (var i = 0; i < length; i++)
		{
			arr[i] = (i*step) + start;
		}

		return arr;
	};
}

if (!Array.union)
{
	/**
	 * Get the union of the supplied input arrays.
	 *
	 * @param [arguments] The arrays to union
	 * @returns {Array}
	 */
	Array.union = function() {
		var obj = {},
			res = [],
			i, j, k;

		for (i in arguments)
		{
			if(arguments.hasOwnProperty(i))
			{
				for (j in arguments[i])
				{
					if (arguments[i].hasOwnProperty(j))
					{
						obj[arguments[i][j]] = arguments[i][j];
					}
				}
			}
		}

		for (k in obj) {
			if (obj.hasOwnProperty(k))
			{
				res.push(obj[k]);
			}
		}

		return res;
	};
}

if (!Array.prototype.union)
{
	/**
	 * Get the union of the current array and the supplied arrays
	 */
	Array.prototype.union = function() {
		var i,
			toUnionize = [this];

		for (i in arguments)
		{
			if (arguments.hasOwnProperty(i))
			{
				toUnionize.push(arguments[i]);
			}
		}

		return Array.union.apply(Array, toUnionize);
	}
}

if (!Array.prototype.intersect)
{
	Array.prototype.intersect = function(arr) {
		arr = arr || [];

		if (!Array.isArray(arr))
		{
			throw new TypeError('Expected parameter of type array');
		}

		return this.filter(function(n) {
			return arr.indexOf(n) != -1;
		});
	}
}