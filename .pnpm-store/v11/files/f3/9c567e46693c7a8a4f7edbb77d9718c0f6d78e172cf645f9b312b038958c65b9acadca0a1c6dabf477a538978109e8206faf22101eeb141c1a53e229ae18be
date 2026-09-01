import { Trigger } from '../interfaces';
import { Player } from '@lordicon/web';
/**
 * The __Loop__ trigger plays the animation from the first to the last frame infinitely, with no interaction necessary.
 */
export declare class Loop implements Trigger {
    protected player: Player;
    protected element: HTMLElement;
    protected targetElement: HTMLElement;
    protected delayTimer: any;
    constructor(player: Player, element: HTMLElement, targetElement: HTMLElement);
    onReady(): void;
    onComplete(): void;
    onDisconnected(): void;
    play(): void;
    protected scheduleDelayedPlay(): void;
    protected resetDelayTimer(): void;
    get delay(): number;
}
