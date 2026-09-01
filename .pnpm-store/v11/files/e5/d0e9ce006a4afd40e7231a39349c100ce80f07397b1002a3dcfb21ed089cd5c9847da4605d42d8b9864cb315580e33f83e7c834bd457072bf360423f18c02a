import { Player } from '@lordicon/web';
import { Trigger } from '../interfaces';
/**
 * The __Hover__ trigger plays the animation from the first to the last frame when the cursor hovers over the icon (target).
 */
export declare class Hover implements Trigger {
    protected player: Player;
    protected element: HTMLElement;
    protected targetElement: HTMLElement;
    protected connected: boolean;
    protected targetState?: string;
    protected delayTimer: any;
    protected intersectionObserver: IntersectionObserver | undefined;
    constructor(player: Player, element: HTMLElement, targetElement: HTMLElement);
    onConnected(): void;
    onDisconnected(): void;
    onComplete(): void;
    onHover(): void;
    onClick(): void;
    play(handleDelay?: boolean): void;
    replay(): void;
    protected scheduleDelayedPlay(): void;
    protected initIntersectionObserver(): void;
    protected resetIntersectionObserver(): void;
    protected resetDelayTimer(): void;
    protected resetState(): void;
    protected cleanup(): void;
    get intro(): string | null;
    get delay(): number;
    get loading(): boolean;
    get clickToReplay(): boolean;
}
