/**
 * Object storing multiple named colors.
 *
 * Example:
 * {
 *     primary: 'red',
 *     secondary: '#ff0000',
 * }
 */
interface ColorMap {
    [key: string]: string;
}
/**
 * Returns a hexadecimal color string for a given color name or hex code.
 *
 * Example:
 * ```js
 * parseColor('red'); // "#ff0000"
 * parseColor('#0f0'); // "#00ff00"
 * ```
 *
 * @param colorName Color name (e.g., "red") or hex string (e.g., "#ff0000" or "#0f0").
 * @returns Hexadecimal color string in the format "#rrggbb".
 */
export declare function parseColor(colorName: string): string;
/**
 * Parses a colors attribute string into a ColorMap object.
 *
 * Example:
 * ```js
 * parseColors('primary:red,secondary:#00ff00');
 * // Returns: { primary: '#ff0000', secondary: '#00ff00' }
 * ```
 *
 * @param colors Colors defined as a comma-separated string (e.g., "primary:red,secondary:#00ff00").
 * @returns Object mapping color names to hex strings, or undefined if input is invalid.
 */
export declare function parseColors(colors: any): ColorMap | undefined;
/**
 * Parses a stroke attribute value to a supported numeric range.
 *
 * @param value Stroke value as a string or number ("light", 1, "1", "regular", 2, "2", "bold", 3, "3").
 * @returns Stroke value as 1, 2, or 3, or undefined if not valid.
 */
export declare function parseStroke(value: any): (1 | 2 | 3 | undefined);
/**
 * Parse state attribute.
 * @param value State value.
 * @returns Returns the state as a string if valid, otherwise undefined.
 */
export declare function parseState(value: any): (string | undefined);
export {};
