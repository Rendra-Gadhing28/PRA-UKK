import { ColorMap, EventHandler, EventName, IconProperties, IconState, LegacyIconProperties, LottieAnimationInstance, LottieData, LottieProperty, PlaybackDirection, Stroke } from './interfaces';
export type { LottieOptions } from './interfaces';
/**
 * Player class for controlling and customizing Lottie-based icons.
 */
export declare class Player {
    protected _container: HTMLElement;
    protected _iconData: any;
    protected _initialProperties: IconProperties & LegacyIconProperties;
    protected _lottieInstance?: LottieAnimationInstance;
    protected _ready: boolean;
    protected _colorsProxy?: any;
    protected _direction: PlaybackDirection;
    protected _speed: number;
    protected _lottieProperties?: LottieProperty[];
    protected _eventHandlers: any;
    protected _state?: IconState;
    protected _availableStates: IconState[];
    protected _animationFrameRate: number;
    /**
     * Creates a new Player instance.
     * @param container The DOM element where the animation will be rendered.
     * @param data Lottie animation data.
     * @param properties Initial icon properties (colors, stroke, state, etc.).
     * @param options Additional options (e.g., autoInit).
     */
    constructor(container: HTMLElement, data: LottieData, properties?: IconProperties & LegacyIconProperties, options?: {
        autoInit?: boolean;
    });
    /**
     * Initializes the player and connects it to the DOM element.
     * Throws an error if already initialized.
     */
    init(): void;
    /**
     * Destroys the player and releases all resources.
     * Throws an error if not initialized.
     */
    destroy(): void;
    /**
     * Registers an event listener for a player event.
     * @param name Event name (e.g., 'complete', 'frame', 'ready').
     * @param handler Handler function to call when the event is triggered.
     * @returns Function to remove the listener.
     */
    addEventListener(name: EventName, handler: EventHandler): () => void;
    /**
     * Removes an event listener for a player event.
     * @param name Event name.
     * @param handler Handler function to remove. If not provided, removes all handlers for the event.
     */
    removeEventListener(name: EventName, handler?: EventHandler): void;
    /**
     * Triggers a player event and invokes all registered callbacks.
     * @param name Event name.
     * @param args Optional arguments to pass to the callbacks.
     */
    protected triggerEvent(name: EventName, args?: any): void;
    /**
     * Forces a re-render of the animation.
     */
    protected refresh(): void;
    /**
     * Starts playing the animation from the current frame.
     * Note: If the animation is finished, it cannot be played again from the last frame.
     */
    play(): void;
    /**
     * Plays the animation from the beginning of the current state or from the start if no state is set.
     */
    playFromStart(): void;
    /**
     * Pauses the animation at the current frame.
     */
    pause(): void;
    /**
     * Stop the animation.
     */
    stop(): void;
    /**
     * Moves the animation to a specific frame and stops.
     * @param frame Frame number to seek to.
     */
    seek(frame: number): void;
    /**
     * Moves the animation to the first frame and stops.
     */
    seekToStart(): void;
    /**
     * Moves the animation to the last frame and stops.
     */
    seekToEnd(): void;
    /**
     * Sets the animation segment to play.
     * If no segment is provided, resets to the default segment.
     * @param segment Optional segment as [start, end] frame numbers.
     */
    switchSegment(segment?: [number, number]): void;
    /**
     * Sets multiple icon properties at once.
     * Any property not provided will be reset to its default value.
     * @param properties Properties to assign.
     */
    set properties(properties: IconProperties);
    /**
     * Gets the current icon properties (colors, stroke, state).
     * @returns The current properties.
     */
    get properties(): IconProperties;
    /**
     * Sets all customizable colors at once.
     * Pass null to reset all colors to default.
     * @param colors Color map or null.
     */
    set colors(colors: ColorMap | null);
    /**
     * Provides a proxy for reading or updating individual colors by name.
     *
     * Example:
     *   player.colors.primary = '#ff0000';
     *   delete player.colors.secondary;
     */
    get colors(): ColorMap;
    /**
     * Sets the stroke width for the icon.
     * Pass null to reset to default.
     * @param stroke Stroke value or null.
     */
    set stroke(stroke: Stroke | null);
    /**
     * Gets the current stroke width of the icon.
     * @returns Stroke value or null if not set.
     */
    get stroke(): Stroke | null;
    /**
     * Sets the current state (animation segment) of the icon.
     * If the state does not exist, falls back to the default state.
     * @param state State name or null for default.
     */
    set state(state: string | null);
    /**
     * Gets the current state (animation segment) of the icon.
     * @returns State name or null if not set.
     */
    get state(): string | null;
    /**
     * Sets the playback speed of the animation.
     * @param speed Playback speed (1 = normal).
     */
    set speed(speed: number);
    /**
     * Gets the current playback speed.
     * @returns Playback speed.
     */
    get speed(): number;
    /**
     * Sets the playback direction.
     * @param direction 1 for forward, -1 for reverse.
     */
    set direction(direction: PlaybackDirection);
    /**
     * Gets the current playback direction.
     * @returns Playback direction.
     */
    get direction(): PlaybackDirection;
    /**
     * Enables or disables looping of the animation.
     * @param loop True to loop, false otherwise.
     */
    set loop(loop: boolean);
    /**
     * Gets whether the animation is set to loop.
     * @returns True if looping, false otherwise.
     */
    get loop(): boolean;
    /**
     * Sets the current frame of the animation.
     * @param frame Frame number.
     */
    set frame(frame: number);
    /**
     * Gets the current frame of the animation.
     * @returns Current frame number.
     */
    get frame(): number;
    /**
     * Gets the list of available states for the icon.
     * @returns Array of available states.
     */
    get availableStates(): IconState[];
    /**
     * Gets the frame rate of the animation.
     * @returns Frame rate in frames per second.
     */
    get frameRate(): number;
    /**
     * Returns true if the animation is currently playing.
     */
    get playing(): boolean;
    /**
     * Returns true if the player is ready for interaction.
     */
    get ready(): boolean;
    /**
     * Gets the total number of frames in the animation.
     * @returns Frame count.
     */
    get frameCount(): number;
    /**
     * Gets the current segment of the animation as [start, end] frame numbers.
     * @returns Segment as [start, end].
     */
    get segment(): [number, number];
    /**
     * Gets the duration of the animation in seconds.
     * @returns Duration in seconds.
     */
    get duration(): number;
    /**
     * Provides access to the underlying Lottie player instance.
     * @returns LottieAnimationInstance.
     */
    get lottieInstance(): LottieAnimationInstance | undefined;
    /**
     * Gets all customizable properties for the icon.
     * @returns Array of LottieProperty.
     */
    get lottieProperties(): LottieProperty[];
}
