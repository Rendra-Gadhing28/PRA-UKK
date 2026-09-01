import { Player } from '@lordicon/web';
import { Trigger } from '../interfaces';
type FrameSegment = [number, number];
type TRIGGER_MODE = 'hover' | 'class' | 'manual';
/**
 * The __Morph__ trigger plays the animation forward (from the first to the last frame) when hovering over the icon,
 * and reverses it (from the last to the first frame) when the cursor leaves.
 * For some states, it plays a part of the animation on enter, and plays the remaining part when the cursor leaves.
 */
export declare class Morph implements Trigger {
    protected player: Player;
    protected element: HTMLElement;
    protected targetElement: HTMLElement;
    /**
     * Animation segments for mouse enter and leave actions.
     * segments[0] - segment for mouse enter
     * segments[1] - segment for mouse leave
     */
    protected segments?: [FrameSegment, FrameSegment];
    /**
     * Queue to manage playback requests.
     */
    protected queue: number[];
    protected mouseIn: boolean;
    protected connected: boolean;
    protected targetState?: string;
    protected delayTimer: any;
    protected mutationTimer: any;
    protected intersectionObserver: IntersectionObserver | undefined;
    protected observer: MutationObserver | undefined;
    constructor(player: Player, element: HTMLElement, targetElement: HTMLElement);
    onConnected(): void;
    onDisconnected(): void;
    onMouseEnter(): void;
    onMouseLeave(): void;
    onComplete(): void;
    onState(): void;
    onClick(): void;
    play(handleDelay?: boolean): void;
    replay(): void;
    triggerEnter(): void;
    triggerLeave(): void;
    protected scheduleDelayedPlay(): void;
    /**
     * Processes the segment queue and plays the next segment if the player is not currently playing.
     */
    protected handleQueue(): void;
    /**
     * Updates the animation segments based on the current player state and parameters.
     */
    protected handleState(): void;
    protected initIntersectionObserver(): void;
    protected resetIntersectionObserver(): void;
    protected initMutationObserver(): void;
    protected resetMutationObserver(): void;
    protected resetDelayTimer(): void;
    protected resetState(): boolean;
    protected resetPlayer(): void;
    protected cleanup(): void;
    get intro(): string | null;
    get delay(): number;
    get loading(): boolean;
    get clickToReplay(): boolean;
    get mode(): [TRIGGER_MODE, string?];
}
export {};
