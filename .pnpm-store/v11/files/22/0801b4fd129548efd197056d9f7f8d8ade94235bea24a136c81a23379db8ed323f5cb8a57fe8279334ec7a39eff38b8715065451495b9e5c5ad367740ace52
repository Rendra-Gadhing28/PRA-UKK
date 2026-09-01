import { Trigger } from '../interfaces';
import { Player } from '@lordicon/web';
/**
 * The __Sequence__ trigger allows you to define complex animation scenarios using a simple sequence definition.
 *
 * Example usage:
 * ```html
 * <lord-icon trigger="sequence" sequence="state:intro-empty,play,state:hover-empty,play,state:morph-fill,play,state:morph-erase,play,state:intro-empty,delay:first:last:500,play:reverse" src="/trash.json"></lord-icon>
 * ```
 */
export declare class Sequence implements Trigger {
    protected player: Player;
    protected element: HTMLElement;
    protected targetElement: HTMLElement;
    protected sequenceIndex: number;
    protected frameState: string | null;
    protected frameDelayFirst: number | null;
    protected frameDelayLast: number | null;
    protected timer: any;
    protected observer: MutationObserver;
    constructor(player: Player, element: HTMLElement, targetElement: HTMLElement);
    onReady(): void;
    onComplete(): void;
    onConnected(): void;
    onDisconnected(): void;
    protected reset(): void;
    protected takeStep(): {
        action: string;
        params: string[];
    };
    protected handleStep(action: string, params: string[]): void;
    protected step(): void;
    get sequence(): string;
    get speed(): number;
}
