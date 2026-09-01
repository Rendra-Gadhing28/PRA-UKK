import { IconProperties, LegacyIconProperties, Player } from '@lordicon/web';
import { IconData, Trigger, TriggerConstructor } from './interfaces';
/**
 * Defines the available strategies for loading icons in the custom element.
 * - 'lazy': Loads the icon when it enters the viewport.
 * - 'interaction': Loads the icon after a user interaction.
 * - 'delay': Loads the icon after a specified delay.
 */
export type LoadingType = 'lazy' | 'interaction' | 'delay';
/**
 * Enumerates all supported attributes for the custom element.
 */
type SUPPORTED_ATTRIBUTES = 'colors' | 'src' | 'state' | 'trigger' | 'loading' | 'target' | 'stroke' | 'speed';
/**
 * The Lordicon custom element class.
 * Handles icon loading, rendering, customization, and interaction logic.
 */
export declare class Element extends HTMLElement {
    protected static _definedTriggers: Map<string, TriggerConstructor>;
    /**
     * Returns the current version of the element.
     */
    static get version(): string;
    /**
     * Returns the list of attributes to observe for changes.
     */
    static get observedAttributes(): SUPPORTED_ATTRIBUTES[];
    /**
     * Registers a custom trigger for icon interaction.
     * Triggers define how the icon responds to user actions.
     * @param name The name of the trigger.
     * @param triggerClass The trigger class constructor.
     */
    static defineTrigger(name: string, triggerClass: TriggerConstructor): void;
    protected _root?: ShadowRoot;
    protected _isConnected: boolean;
    protected _ready: boolean;
    protected _assignedIconData?: IconData;
    protected _loadedIconData?: IconData;
    protected _triggerInstance?: Trigger;
    protected _playerInstance?: Player;
    protected _animationContainer?: HTMLElement;
    /**
     * Stores a callback for deferred icon loading, used by lazy/interation/delay strategies.
     */
    delayedLoading: ((cancel?: boolean) => void) | null;
    /**
     * Handles changes to observed attributes and delegates to the appropriate handler.
     * @param name The attribute name.
     * @param oldValue The previous value.
     * @param newValue The new value.
     */
    protected attributeChangedCallback(name: SUPPORTED_ATTRIBUTES, _oldValue: any, _newValue: any): void;
    /**
     * Called when the element is added to the DOM.
     * Sets up shadow DOM, styles, and loading strategy.
     */
    protected connectedCallback(): void;
    /**
     * Called when the element is removed from the DOM.
     * Cleans up any resources and event listeners.
     */
    protected disconnectedCallback(): void;
    /**
     * Finds a target element by traversing up the DOM tree.
     * It first attempts to find the target using `closest()`. If that fails,
     * it falls back to a method that can traverse across Shadow DOM boundaries.
     * @param selector The CSS selector for the target element.
     * @returns The found HTMLElement or null.
     */
    protected findTarget(selector: string): HTMLElement | null;
    /**
     * Helper method to find a target by traversing up from a starting element,
     * crossing shadow boundaries if necessary.
     * @param startElement The element to start searching from.
     * @param selector The CSS selector for the target element.
     * @returns The found HTMLElement or null.
     */
    private findTargetAcrossShadowBoundaries;
    /**
     * Creates the shadow DOM structure and attaches styles and slots.
     */
    protected createElements(): void;
    /**
     * Creates a slot element inside the shadow DOM for projecting light DOM content.
     */
    protected createSlot(): void;
    /**
     * Destroys the slot element from the shadow DOM.
     */
    protected destroySlot(): void;
    /**
     * Factory method for creating a Player instance.
     * Can be overridden for custom player instantiation.
     */
    protected playerFactory(container: HTMLElement, iconData: IconData, properties: IconProperties & LegacyIconProperties): Player;
    /**
     * Instantiates the Player and sets up dynamic styles, triggers, and event listeners.
     * Handles asynchronous icon data loading.
     */
    protected createPlayer(): Promise<void>;
    /**
     * Destroys the Player and Trigger instances, cleaning up all resources.
     * Called when the icon data changes or the element is disconnected.
     */
    protected destroyPlayer(): void;
    /**
     * Loads icon data from the 'src' attribute or uses the assigned icon data.
     * Returns the icon data object or undefined if loading fails.
     */
    protected loadIconData(): Promise<IconData>;
    /**
     * Synchronizes the element's state with the Player instance.
     * Updates CSS variables and other dynamic properties.
     */
    protected refresh(): void;
    /**
     * Updates CSS variables for icon colors based on the Player's palette.
     * CSS variables take precedence over other color assignments.
     */
    protected movePaletteToCssVariables(): void;
    /**
     * Called when the 'target' attribute changes.
     * Reloads the trigger to use the new target element.
     */
    protected targetChanged(): void;
    /**
     * Called when the 'loading' attribute changes.
     */
    protected loadingChanged(): void;
    /**
     * Called when the 'trigger' attribute changes.
     * Disconnects the old trigger and instantiates the new one.
     */
    protected triggerChanged(): void;
    /**
     * Called when the 'colors' attribute changes.
     * Updates the Player's color palette.
     */
    protected colorsChanged(): void;
    /**
     * Called when the 'stroke' attribute changes.
     * Updates the Player's stroke width.
     */
    protected strokeChanged(): void;
    /**
     * Called when the 'speed' attribute changes.
     * Updates the Player's animation speed.
     */
    protected speedChanged(): void;
    /**
     * Called when the 'state' attribute changes.
     * Updates the Player's animation state.
     */
    protected stateChanged(): void;
    /**
     * Called when the 'icon' attribute changes.
     * Reloads the Player with the new icon.
     */
    protected iconChanged(): void;
    /**
     * Called when the 'src' attribute changes.
     * Reloads the Player with the new icon source.
     */
    protected srcChanged(): void;
    /**
     * Directly assigns icon data to the element.
     * Triggers a reload if the data changes.
     */
    set icon(value: IconData | undefined);
    /**
     * Gets the currently assigned or loaded icon data.
     */
    get icon(): IconData | undefined;
    /**
     * Sets the 'src' attribute for loading icon data from a URL.
     */
    set src(value: string | null);
    /**
     * Gets the current 'src' attribute value.
     */
    get src(): string | null;
    /**
     * Sets the animation state for the icon.
     * You can check available states from the player instance.
     */
    set state(value: string | null);
    /**
     * Gets the current animation state.
     */
    get state(): string | null;
    /**
     * Sets the color palette for the icon.
     * Accepts a comma-separated string, e.g. 'primary:#fdd394,secondary:#03a9f4'.
     */
    set colors(value: string | null);
    /**
     * Gets the current color palette string.
     */
    get colors(): string | null;
    /**
     * Sets the trigger name for icon interaction.
     * The trigger must be registered beforehand.
     */
    set trigger(value: string | null);
    /**
     * Gets the current trigger name.
     */
    get trigger(): string | null;
    /**
     * Sets the loading strategy for the icon.
     * Options: 'lazy', 'interaction', or 'delay'.
     */
    set loading(value: LoadingType | null);
    /**
     * Gets the current loading strategy.
     */
    get loading(): LoadingType | null;
    /**
     * Sets the CSS selector for the target element used for event listening.
     */
    set target(value: string | null);
    /**
     * Gets the current target selector.
     */
    get target(): string | null;
    /**
     * Sets the stroke style for the icon (e.g., 1, 2, 3, light, regular, bold).
     */
    set stroke(value: string | null);
    /**
     * Gets the current stroke width.
     */
    get stroke(): string | null;
    /**
     * Sets the animation speed for the icon.
     * Accepts a number or a string that can be parsed to a number.
     */
    set speed(value: string | number | null);
    /**
     * Gets the current animation speed.
     * Returns 1 if not set or invalid.
     */
    get speed(): number;
    /**
     * Returns true if the element is fully initialized and ready for interaction.
     * You can listen for the 'ready' event to detect readiness.
     */
    get ready(): boolean;
    /**
     * Returns a promise that resolves when the element is ready.
     * Useful for awaiting initialization in external code.
     */
    get readyPromise(): Promise<void>;
    /**
     * Returns the Player instance associated with this element.
     */
    get playerInstance(): Player | undefined;
    /**
     * Returns the Trigger instance associated with this element.
     */
    get triggerInstance(): Trigger | undefined;
    /**
     * Returns the animation container element inside the shadow DOM.
     */
    get animationContainer(): HTMLElement | undefined;
}
export {};
