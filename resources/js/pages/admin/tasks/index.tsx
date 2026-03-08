import AppLayout from '@/layouts/app-layout';
import { Head, router } from '@inertiajs/react';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Input } from '@/components/ui/input';
import Pagination from '@/components/pagination';
import { useState, useEffect, useCallback } from 'react';
import { debounce } from 'lodash';
import { route } from 'ziggy-js';
import { Button } from '@/components/ui/button';


interface Executor {
    id: string;
    email: string;
    userInfo?: {
        first_name: string;
        last_name: string;
    };
}

interface Task {
    id: string;
    title: string;
    description: string;
    creator: {
        email: string;
        userInfo?: {
            first_name: string;
            last_name: string;
        };
    };
    executors: Executor[];
}

interface Props {
    tasks: {
        data: Task[];
        links: any[];
    };
    filters: {
        search: string;
    };
}

export default function Index({ tasks, filters }: Props) {
    const [search, setSearch] = useState(filters.search || '');

    const handleSearch = useCallback(
        debounce((value: string) => {
            router.get(
                route('tasks.index'),
                { search: value },
                { preserveState: true, replace: true }
            );
        }, 300),
        []
    );

    useEffect(() => {
        handleSearch(search);
    }, [search, handleSearch]);

    return (
        <AppLayout breadcrumbs={[{ title: 'Tasks', href: '/tasks' }]}>
            <Head title="Tasks" />

            <div className="p-6">
                <h1 className="text-2xl font-semibold mb-2">Tasks</h1>
                <div className="flex justify-between items-center mb-6">
                    <div className="w-1/2">
                        <Input
                            placeholder="Search tasks..."
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                        />
                    </div>
                    <Button onClick={() => router.get(route('tasks.create'))} className='hover:bg-dark-500 hover:cursor-pointer'>Create</Button>
                </div>

                <div className="bg-white rounded-md border">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Title</TableHead>
                                <TableHead>Description</TableHead>
                                <TableHead>Creator</TableHead>
                                <TableHead>Executors</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {tasks.data.length > 0 ? (
                                tasks.data.map((task) => (
                                    <TableRow key={task.id} className="hover:bg-slate-50 cursor-pointer" onClick={() => router.get(route('tasks.show', task.id))}>
                                        <TableCell className="font-medium text-primary hover:underline">
                                            {task.title}
                                        </TableCell>
                                        <TableCell>
                                            <div className="max-w-[400px] line-clamp-2 text-sm text-muted-foreground whitespace-pre-wrap">
                                                {task.description}
                                            </div>
                                        </TableCell>
                                        <TableCell>
                                            {task.creator.userInfo ? `${task.creator.userInfo.first_name} ${task.creator.userInfo.last_name}` : task.creator.email}
                                        </TableCell>
                                        <TableCell>
                                            <div className="flex items-center gap-1.5 flex-wrap">
                                                {task.executors.length > 0 ? (
                                                    <>
                                                        <span className="text-sm">
                                                            {task.executors.slice(0, 2).map((e) => e.userInfo ? `${e.userInfo.first_name} ${e.userInfo.last_name}` : e.email).join(', ')}
                                                        </span>
                                                        {task.executors.length > 2 && (
                                                            <span className="text-[10px] bg-slate-100 text-muted-foreground px-1.5 py-0.5 rounded-full font-medium whitespace-nowrap">
                                                                +{task.executors.length - 2} more
                                                            </span>
                                                        )}
                                                    </>
                                                ) : (
                                                    <span className="text-muted-foreground italic text-xs">No executors</span>
                                                )}
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                ))
                            ) : (
                                <TableRow>
                                    <TableCell colSpan={4} className="text-center py-6 text-muted-foreground">
                                        No tasks found.
                                    </TableCell>
                                </TableRow>
                            )}
                        </TableBody>
                    </Table>
                </div>

                <Pagination links={tasks.links} />
            </div>
        </AppLayout>
    );
}
