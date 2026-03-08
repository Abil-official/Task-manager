import AppLayout from '@/layouts/app-layout';
import { Head, useForm, router } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Checkbox } from '@/components/ui/checkbox';
import { Calendar } from '@/components/ui/calendar';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { CalendarIcon, Search, User as UserIcon, Clock, AlignLeft } from 'lucide-react';
import { format } from 'date-fns';
import { cn } from '@/lib/utils';
import { useState, useCallback, useEffect } from 'react';
import { debounce } from 'lodash';
import Pagination from '@/components/pagination';
import { route } from 'ziggy-js';

interface User {
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
    due_date: string | null;
    executors: User[];
}

interface Props {
    users: {
        data: User[];
        links: any[];
    };
    task: Task;
}

export default function Edit({ users, task }: Props) {
    const { data, setData, put, processing, errors } = useForm({
        title: task.title || '',
        description: task.description || '',
        due_date: task.due_date ? new Date(task.due_date) : null,
        executor_ids: task.executors ? task.executors.map(u => u.id) : [],
    });

    const [userSearch, setUserSearch] = useState('');
    const [userRegistry, setUserRegistry] = useState<Record<string, User>>({});

    // Initialize registry with existing executors if they aren't in current users page
    useEffect(() => {
        if (task.executors) {
            setUserRegistry(prev => {
                const next = { ...prev };
                task.executors.forEach(user => {
                    next[user.id] = user;
                });
                return next;
            });
        }
    }, [task.executors]);

    // Update registry when new users are loaded
    useEffect(() => {
        if (users.data) {
            setUserRegistry(prev => {
                const next = { ...prev };
                users.data.forEach(user => {
                    next[user.id] = user;
                });
                return next;
            });
        }
    }, [users.data]);

    const handleUserSearch = useCallback(
        debounce((value: string) => {
            router.get(
                route('tasks.edit', task.id),
                { search: value },
                { preserveState: true, replace: true, preserveScroll: true }
            );
        }, 300),
        [task.id]
    );

    useEffect(() => {
        handleSearchChange(userSearch);
    }, [userSearch]);

    // This is just a helper for the useEffect below
    const handleSearchChange = (value: string) => {
        handleUserSearch(value);
    };

    const toggleUser = (userId: string) => {
        setData('executor_ids',
            data.executor_ids.includes(userId)
                ? data.executor_ids.filter(id => id !== userId)
                : [...data.executor_ids, userId]
        );
    };

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        put(route('tasks.update', task.id));
    };

    // Get selected users for preview from registry
    const selectedUsers = data.executor_ids.map(id => userRegistry[id]).filter(Boolean);

    return (
        <AppLayout breadcrumbs={[{ title: 'Tasks', href: route('tasks.index') }, { title: task.title, href: route('tasks.show', task.id) }, { title: 'Edit', href: route('tasks.edit', task.id) }]}>
            <Head title={`Edit Task: ${task.title}`} />

            <div className="p-6">
                <div className="mb-8">
                    <h1 className="text-3xl font-bold tracking-tight">Edit Task</h1>
                    <p className="text-muted-foreground">Modify task details or update assignments.</p>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                    {/* Left Side: Form (Span 7) */}
                    <div className="lg:col-span-7 space-y-8">
                        <form onSubmit={submit} className="space-y-8">
                            <Card className="shadow-sm">
                                <CardHeader>
                                    <CardTitle>Global Information</CardTitle>
                                    <CardDescription>Primary details about the task purpose and timeline.</CardDescription>
                                </CardHeader>
                                <CardContent className="space-y-6">
                                    <div className="space-y-2">
                                        <Label htmlFor="title">Task Title</Label>
                                        <Input
                                            id="title"
                                            className="h-11"
                                            value={data.title}
                                            onChange={e => setData('title', e.target.value)}
                                            placeholder="What needs to be done?"
                                        />
                                        {errors.title && <p className="text-sm text-destructive">{errors.title}</p>}
                                    </div>

                                    <div className="space-y-2">
                                        <Label htmlFor="description">Description</Label>
                                        <Textarea
                                            id="description"
                                            value={data.description}
                                            onChange={e => setData('description', e.target.value)}
                                            placeholder="Provide context and instructions..."
                                            rows={5}
                                        />
                                        {errors.description && <p className="text-sm text-destructive">{errors.description}</p>}
                                    </div>

                                    <div className="space-y-2 flex flex-col">
                                        <Label>Due Date</Label>
                                        <Popover>
                                            <PopoverTrigger asChild>
                                                <Button
                                                    variant={"outline"}
                                                    className={cn(
                                                        "w-full h-11 justify-start text-left font-normal border-dashed",
                                                        !data.due_date && "text-muted-foreground"
                                                    )}
                                                >
                                                    <CalendarIcon className="mr-2 h-4 w-4" />
                                                    {data.due_date ? format(data.due_date, "PPP") : <span>Set a deadline</span>}
                                                </Button>
                                            </PopoverTrigger>
                                            <PopoverContent className="w-auto p-0" align="start">
                                                <Calendar
                                                    mode="single"
                                                    selected={data.due_date || undefined}
                                                    onSelect={date => setData('due_date', date || null)}
                                                    initialFocus
                                                />
                                            </PopoverContent>
                                        </Popover>
                                        {errors.due_date && <p className="text-sm text-destructive">{errors.due_date}</p>}
                                    </div>
                                </CardContent>
                            </Card>

                            <Card className="shadow-sm">
                                <CardHeader>
                                    <CardTitle>Executors</CardTitle>
                                    <CardDescription>Assign this task to one or more members.</CardDescription>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    <div className="relative">
                                        <Search className="absolute left-3 top-3 h-4 w-4 text-muted-foreground" />
                                        <Input
                                            placeholder="Search team members..."
                                            className="pl-9 h-10"
                                            value={userSearch}
                                            onChange={e => setUserSearch(e.target.value)}
                                        />
                                    </div>

                                    <div className="border rounded-lg overflow-hidden divide-y bg-slate-50/30 max-h-[200px] overflow-y-auto custom-scrollbar">
                                        {users.data.length > 0 ? (
                                            users.data.map((user) => (
                                                <div key={user.id} className="flex items-center space-x-3 p-3.5 hover:bg-white transition-colors group">
                                                    <Checkbox
                                                        id={`user-${user.id}`}
                                                        checked={data.executor_ids.includes(user.id)}
                                                        onCheckedChange={() => toggleUser(user.id)}
                                                    />
                                                    <label
                                                        htmlFor={`user-${user.id}`}
                                                        className="text-sm font-medium leading-none cursor-pointer flex-grow py-1 flex flex-col gap-0.5"
                                                    >
                                                        <span>{user.userInfo ? `${user.userInfo.first_name} ${user.userInfo.last_name}` : user.email}</span>
                                                        {user.userInfo && <span className="text-[10px] text-muted-foreground font-normal">{user.email}</span>}
                                                    </label>
                                                </div>
                                            ))
                                        ) : (
                                            <div className="p-8 text-center text-sm text-muted-foreground italic">
                                                No users matching "{userSearch}"
                                            </div>
                                        )}
                                    </div>
                                    <Pagination links={users.links} only={['users']} preserveScroll={true} />
                                    {errors.executor_ids && <p className="text-sm text-destructive">{errors.executor_ids}</p>}
                                </CardContent>
                            </Card>

                            <div className="flex items-center justify-end space-x-4">
                                <Button type="button" variant="ghost" className="px-6" onClick={() => window.history.back()}>
                                    Discard
                                </Button>
                                <Button type="submit" disabled={processing} className="px-8 shadow-md">
                                    Update Task
                                </Button>
                            </div>
                        </form>
                    </div>

                    {/* Right Side: Quick View (Span 5) */}
                    <div className="lg:col-span-5 sticky top-6">
                        <div className="mb-3 px-1 flex items-center justify-between">
                            <h2 className="text-sm font-bold uppercase tracking-wider text-muted-foreground flex items-center gap-2">
                                <Clock className="w-4 h-4" /> Quick View
                            </h2>
                            <span className="text-[10px] bg-primary/10 text-primary px-2 py-0.5 rounded-full font-bold">LIVE PREVIEW</span>
                        </div>

                        <Card className="border-2 border-primary/10 bg-gradient-to-br from-white to-slate-50/50 shadow-lg overflow-hidden">
                            <div className="h-1.5 bg-primary w-full" />
                            <CardHeader className="pb-4">
                                <div className="flex items-start justify-between gap-4">
                                    <div className="space-y-1.5 flex-1">
                                        <CardTitle className={cn(
                                            "text-xl break-words leading-tight transition-all duration-300",
                                            !data.title && "text-muted-foreground italic font-normal"
                                        )}>
                                            {data.title || "Untitled Task"}
                                        </CardTitle>
                                        <div className="flex items-center gap-4 text-xs font-medium text-muted-foreground">
                                            <div className="flex items-center gap-1.5 bg-slate-100 px-2 py-1 rounded">
                                                <CalendarIcon className="w-3.5 h-3.5" />
                                                {data.due_date ? format(data.due_date, "MMM d, yyyy") : "No date"}
                                            </div>
                                            <div className="flex items-center gap-1.5 bg-slate-100 px-2 py-1 rounded">
                                                <UserIcon className="w-3.5 h-3.5" />
                                                {data.executor_ids.length} assigned
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </CardHeader>
                            <CardContent className="space-y-6">
                                {/* Description Block */}
                                <div className="space-y-2">
                                    <div className="flex items-center gap-2 text-[10px] font-bold text-muted-foreground uppercase tracking-widest">
                                        <AlignLeft className="w-3 h-3" /> Description
                                    </div>
                                    <div className={cn(
                                        "text-sm leading-relaxed whitespace-pre-wrap min-h-[60px] max-h-[150px] overflow-y-auto pr-2 custom-scrollbar",
                                        !data.description && "text-muted-foreground/60 italic border-l-2 border-slate-100 pl-3 py-1"
                                    )}>
                                        {data.description || "The task description will appear here as you type..."}
                                    </div>
                                </div>

                                {/* Selected Executors Block */}
                                <div className="space-y-3">
                                    <div className="flex items-center justify-between">
                                        <div className="flex items-center gap-2 text-[10px] font-bold text-muted-foreground uppercase tracking-widest">
                                            <UserIcon className="w-3 h-3" /> Assignment List
                                        </div>
                                    </div>
                                    <div className="space-y-2">
                                        {selectedUsers.length > 0 ? (
                                            <div className="flex flex-wrap gap-1.5">
                                                {selectedUsers.map(user => (
                                                    <div
                                                        key={`preview-${user.id}`}
                                                        className="bg-white border text-[11px] px-2.5 py-1 rounded-md shadow-sm border-primary/10 flex items-center gap-1.5 animate-in fade-in zoom-in duration-200"
                                                    >
                                                        <div className="w-1.5 h-1.5 rounded-full bg-primary/60" />
                                                        {user.userInfo ? `${user.userInfo.first_name} ${user.userInfo.last_name}` : user.email}
                                                    </div>
                                                ))}
                                            </div>
                                        ) : (
                                            <div className="bg-slate-100/50 rounded-lg p-4 border border-dashed text-center">
                                                <p className="text-xs text-muted-foreground">No executors selected yet.</p>
                                            </div>
                                        )}
                                    </div>
                                </div>

                                <div className="pt-2">
                                    <div className="bg-primary/5 rounded-lg p-3 border-2 border-primary/20 flex items-center justify-between transition-all">
                                        <div className="text-[10px] font-bold text-primary tracking-widest">EDIT MODE</div>
                                        <div className="text-[10px] bg-primary text-white px-2 py-0.5 rounded font-bold uppercase tracking-wide">Ready to Sync</div>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
