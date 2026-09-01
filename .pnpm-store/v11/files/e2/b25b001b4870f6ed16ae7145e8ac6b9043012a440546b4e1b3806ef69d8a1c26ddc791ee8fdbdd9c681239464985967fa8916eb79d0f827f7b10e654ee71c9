import { Player } from '@lordicon/web';
import { Trigger } from '../interfaces';
/**
 * The __In__ trigger plays the animation when the icon (target) enters the user's viewport.
 */
export declare class In implements Trigger {
    protected player: Player;
    protected element: HTMLElement;
    protected targetElement: HTMLElement;
    protected connected: boolean;
    protected delayTimer: any;
    protected intersectionObserver: IntersectionObserver | undefined;
    constructor(player: Player, element: HTMLElement, targetElement: HTMLElement);
    onConnected(): void;
    onDisconnected(): void;
    onClick(): void;
    play(handleDelay?: boolean): void;
    protected scheduleDelayedPlay(): void;
    protected initIntersectionObserver(): void;
    protected resetIntersectionObserver(): void;
    protected resetDelayTimer(): void;
    protected cleanup(): void;
    get delay(): number;
    get loading(): boolean;
    get clickToReplay(): boolean;
}
