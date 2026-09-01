import { Trigger } from '../interfaces';
import { Player } from '@lordicon/web';
type FrameSegment = [number, number];
/**
 * The __Boomerang__ trigger plays the animation forward when you hover over the element,
 * and after reaching the end, it automatically plays in reverse.
 */
export declare class Boomerang implements Trigger {
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
    protected connected: boolean;
    protected targetState?: string;
    protected delayTimer: any;
    protected intersectionObserver: IntersectionObserver | undefined;
    constructor(player: Player, element: HTMLElement, targetElement: HTMLElement);
    onConnected(): void;
    onDisconnected(): void;
    onMouseEnter(): void;
    onComplete(): void;
    onState(): void;
    onClick(): void;
    play(handleDelay?: boolean): void;
    replay(): void;
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
    protected resetDelayTimer(): void;
    protected resetState(): boolean;
    protected resetPlayer(): void;
    protected cleanup(): void;
    get intro(): string | null;
    get delay(): number;
    get loading(): boolean;
    get clickToReplay(): boolean;
}
export {};
