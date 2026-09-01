import { LottieAnimationInstance, LottieData, LottieProperty, RgbColor, RgbTuple } from './interfaces';
/**
 * Converts an RGB color object to a hexadecimal color string.
 * @param value Color object with r, g, b properties.
 * @returns Hexadecimal color string (e.g., "#ff0000").
 */
export declare function rgbToHex(value: RgbColor): string;
/**
 * Converts a hexadecimal color string to an RGB color object.
 * @param hex Hexadecimal color string (with or without "#").
 * @returns RgbColor object with r, g, b properties.
 */
export declare function hexToRgb(hex: string): RgbColor;
/**
 * Converts a hexadecimal color string to an RGB tuple in the 0-1 range.
 * @param hex Hexadecimal color string.
 * @returns RgbTuple with values normalized to 0-1.
 */
export declare function hexToTupleColor(hex: string): RgbTuple;
/**
 * Converts an RGB tuple (0-1 range) to a hexadecimal color string.
 * @param value RgbTuple with values in the 0-1 range.
 * @returns Hexadecimal color string.
 */
export declare function tupleColorToHex(value: RgbTuple): string;
/**
 * Extracts all supported customizable properties from Lottie data.
 * @param data Lottie animation data.
 * @param options Extraction options (e.g., lottieInstance: boolean).
 * @returns Array of LottieProperty objects describing customizable properties.
 */
export declare function extractLottieProperties(data: LottieData, { lottieInstance }?: {
    lottieInstance?: boolean;
}): LottieProperty[];
/**
 * Resets Lottie data or animation instance to default values for the given properties.
 * @param data Lottie data or animation instance to reset.
 * @param properties Array of properties to reset.
 */
export declare function resetLottieProperties(data: LottieData | LottieAnimationInstance, properties: LottieProperty[]): void;
/**
 * Updates Lottie data or animation instance with a new value for the given properties.
 * Handles color, point, and other property types accordingly.
 * @param data Lottie data or animation instance to update.
 * @param properties Array of properties to update.
 * @param value New value to set for each property.
 */
export declare function updateLottieProperties(data: LottieData | LottieAnimationInstance, properties: LottieProperty[], value: any): void;
