import { Trigger } from '../interfaces';
import { Player } from '@lordicon/web';
/**
 * The __Click__ trigger plays the animation when the icon (its target) is clicked or tapped.
 */
export declare class Click implements Trigger {
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
}
