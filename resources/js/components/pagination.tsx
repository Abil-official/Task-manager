import { Link } from '@inertiajs/react';
import { cn } from '@/lib/utils';

interface LinkItem {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginationProps {
    links: LinkItem[];
    only?: string[];
    preserveScroll?: boolean;
}

export default function Pagination({ links, only = [], preserveScroll = false }: PaginationProps) {
    if (links.length <= 3) return null;

    return (
        <div className="flex flex-wrap mt-4 gap-1">
            {links.map((link, key) => (
                link.url === null ? (
                    <div
                        key={key}
                        className="px-3 py-1.5 text-xs leading-4 text-muted-foreground border rounded bg-slate-50/50 cursor-not-allowed"
                        dangerouslySetInnerHTML={{ __html: link.label }}
                    />
                ) : (
                    <Link
                        key={key}
                        className={cn(
                            "px-3 py-1.5 text-xs leading-4 border rounded hover:bg-accent transition-colors",
                            link.active ? 'bg-primary text-primary-foreground font-bold border-primary' : 'bg-white'
                        )}
                        href={link.url}
                        only={only}
                        preserveScroll={preserveScroll}
                        preserveState={true}
                        dangerouslySetInnerHTML={{ __html: link.label }}
                    />
                )
            ))}
        </div>
    );
}
