import { Trigger } from '../interfaces';
import { Player } from '@lordicon/web';
/**
 * The __LoopOnHover__ trigger plays the animation from the first to the last frame in an infinite loop while the cursor hovers over the icon (target).
 */
export declare class LoopOnHover implements Trigger {
    protected player: Player;
    protected element: HTMLElement;
    protected targetElement: HTMLElement;
    protected delayTimer: any;
    protected mouseIn: boolean;
    constructor(player: Player, element: HTMLElement, targetElement: HTMLElement);
    onConnected(): void;
    onDisconnected(): void;
    onMouseEnter(): void;
    onMouseLeave(): void;
    onComplete(): void;
    play(): void;
    protected scheduleDelayedPlay(): void;
    protected resetDelayTimer(): void;
    get delay(): number;
}
