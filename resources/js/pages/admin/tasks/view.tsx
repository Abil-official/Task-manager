import AppLayout from '@/layouts/app-layout';
import { Head, router } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { CalendarIcon, User as UserIcon, Clock, AlignLeft, ArrowLeft, MoreVertical, Edit } from 'lucide-react';
import { format } from 'date-fns';
import { cn } from '@/lib/utils';
import { route } from 'ziggy-js';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { usePage } from '@inertiajs/react';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Loader2, CheckCircle2 } from 'lucide-react';
import { useState } from 'react';

interface UserInfo {
    first_name: string;
    last_name: string;
}

interface TaskLog {
    id: string;
    action: string;
    description: string;
    created_at: string;
    user?: {
        email: string;
        userInfo?: UserInfo;
    };
}

interface Executor {
    id: string;
    email: string;
    userInfo?: UserInfo;
    pivot: {
        status: string;
    };
}

interface Task {
    id: string;
    title: string;
    description: string;
    due_date: string | null;
    creator: {
        id: string;
        email: string;
        userInfo?: UserInfo;
    };
    executors: Executor[];
    task_logs: TaskLog[];
    created_at: string;
}

interface Props {
    task: Task;
}

export default function View({ task }: Props) {
    const { auth } = usePage().props as any;
    const [isUpdating, setIsUpdating] = useState(false);

    const currentUserExecutor = task.executors.find(e => e.id === auth.user.id);
    const currentStatus = currentUserExecutor?.pivot.status || 'pending';

    const handleStatusChange = (newStatus: string) => {
        setIsUpdating(true);
        router.put(route('tasks.update-status', task.id), {
            id: task.id,
            status: newStatus
        }, {
            onFinish: () => setIsUpdating(false)
        });
    };

    const formattedDueDate = task.due_date ? format(new Date(task.due_date), "PPP") : "No deadline set";
    const formattedCreatedAt = format(new Date(task.created_at), "PPP");

    const getInitials = (user: { email: string; userInfo?: UserInfo }) => {
        if (user.userInfo) {
            return `${user.userInfo.first_name.charAt(0)}${user.userInfo.last_name.charAt(0)}`.toUpperCase();
        }
        return user.email.charAt(0).toUpperCase();
    };

    const getFullName = (user: { email: string; userInfo?: UserInfo }) => {
        if (user.userInfo) {
            return `${user.userInfo.first_name} ${user.userInfo.last_name}`;
        }
        return user.email;
    };

    return (
        <AppLayout breadcrumbs={[{ title: 'Tasks', href: route('tasks.index') }, { title: 'Detail', href: route('tasks.show', task.id) }]}>
            <Head title={`Task: ${task.title}`} />

            <div className="p-6">
                {/* Header Actions */}
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
                    <div className="flex items-center gap-3">
                        <Button
                            variant="ghost"
                            size="icon"
                            className="rounded-full hover:bg-slate-100"
                            onClick={() => router.get(route('tasks.index'))}
                        >
                            <ArrowLeft className="w-5 h-5" />
                        </Button>
                        <div>
                            <h1 className="text-3xl font-bold tracking-tight">{task.title}</h1>
                            <p className="text-muted-foreground text-sm">Created on {formattedCreatedAt}</p>
                        </div>
                    </div>
                    <div className="flex items-center gap-2">
                        <Button variant="outline" className="gap-2 hover:cursor-pointer" onClick={() => router.visit(route('tasks.edit', task.id))} >
                            <Edit className="w-4 h-4" /> Edit Task
                        </Button>
                        <Popover>
                            <PopoverTrigger asChild>
                                <Button variant="ghost" size="icon">
                                    <MoreVertical className="w-5 h-5" />
                                </Button>
                            </PopoverTrigger>
                            <PopoverContent align="end" className="w-48 p-2">
                                <Button variant="ghost" className="w-full justify-start text-destructive hover:text-destructive hover:bg-destructive/10">
                                    Delete Task
                                </Button>
                            </PopoverContent>
                        </Popover>
                    </div>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                    {/* Main Content */}
                    <div className="lg:col-span-8 space-y-6">
                        <Card className="shadow-sm border-none bg-white">
                            <CardHeader className="border-b bg-slate-50/30">
                                <div className="flex items-center gap-2 text-[10px] font-bold text-muted-foreground uppercase tracking-widest mb-1">
                                    <AlignLeft className="w-3.5 h-3.5" /> Task Description
                                </div>
                                <CardTitle className="text-lg">Full Content</CardTitle>
                            </CardHeader>
                            <CardContent className="pt-6">
                                <div className={cn(
                                    "text-base leading-relaxed whitespace-pre-wrap text-slate-700",
                                    !task.description && "italic text-muted-foreground"
                                )}>
                                    {task.description || "No description provided for this task."}
                                </div>
                            </CardContent>
                        </Card>

                        {/* Activity Log */}
                        <div className="pt-4 px-1">
                            <h3 className="text-sm font-bold uppercase tracking-wider text-muted-foreground flex items-center gap-2 mb-4">
                                <Clock className="w-4 h-4" /> Activity Log
                            </h3>
                            <div className="space-y-6 relative before:absolute before:inset-0 before:left-2.5 before:w-0.5 before:bg-slate-100 pl-8 pb-4">
                                {task.task_logs && task.task_logs.length > 0 ? (
                                    task.task_logs.map((log) => (
                                        <div key={log.id} className="relative group">
                                            <div className="absolute -left-[30px] top-1.5 w-5 h-5 rounded-full bg-white border-2 border-primary shadow-sm z-10 group-hover:scale-110 transition-transform flex items-center justify-center">
                                                <div className="w-1.5 h-1.5 rounded-full bg-primary" />
                                            </div>
                                            <div className="flex flex-col gap-0.5">
                                                <p className="text-sm font-semibold text-slate-900">{log.description}</p>
                                                <div className="flex items-center gap-2 text-[10px] text-muted-foreground">
                                                    <span className="font-medium text-primary/70">{log.user ? getFullName(log.user) : 'System'}</span>
                                                    <span>•</span>
                                                    <span>{format(new Date(log.created_at), "MMM d, h:mm a")}</span>
                                                </div>
                                            </div>
                                        </div>
                                    ))
                                ) : (
                                    <div className="relative">
                                        <div className="absolute -left-[27px] top-1 w-4 h-4 rounded-full bg-primary border-4 border-white shadow-sm" />
                                        <p className="text-sm font-medium">Task created</p>
                                        <p className="text-xs text-muted-foreground">{formattedCreatedAt}</p>
                                    </div>
                                )}
                            </div>
                        </div>
                    </div>

                    {/* Sidebar Details */}
                    <div className="lg:col-span-4 space-y-6">
                        <Card className="shadow-sm overflow-hidden border-2 border-primary/5">
                            <div className="h-1.5 bg-primary w-full" />
                            <CardHeader className="pb-4">
                                <CardTitle className="text-sm font-bold text-muted-foreground uppercase tracking-wider">Metadata</CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-6">
                                {/* My Status Update Section */}
                                {currentUserExecutor && (
                                    <div className="p-3 rounded-lg bg-primary/5 border border-primary/10 space-y-3">
                                        <div className="flex items-center justify-between">
                                            <div className="text-[10px] font-bold text-primary uppercase tracking-widest flex items-center gap-1.5">
                                                <CheckCircle2 className="w-3 h-3" /> Your Assignment Status
                                            </div>
                                        </div>
                                        <Select
                                            disabled={isUpdating}
                                            value={currentStatus}
                                            onValueChange={handleStatusChange}
                                        >
                                            <SelectTrigger className="h-9 bg-white border-primary/20 hover:border-primary/40 transition-colors">
                                                {isUpdating ? (
                                                    <div className="flex items-center gap-2 text-xs text-muted-foreground italic">
                                                        <Loader2 className="w-3 h-3 animate-spin" /> Updating...
                                                    </div>
                                                ) : (
                                                    <SelectValue placeholder="Select status" />
                                                )}
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="pending" className="text-xs">Pending</SelectItem>
                                                <SelectItem value="in_progress" className="text-xs">In Progress</SelectItem>
                                                <SelectItem value="completed" className="text-xs text-green-600 font-medium">Completed</SelectItem>
                                            </SelectContent>
                                        </Select>
                                    </div>
                                )}


                                {/* Due Date */}
                                <div className="space-y-2">
                                    <div className="text-[10px] font-bold text-muted-foreground uppercase tracking-widest">Deadline</div>
                                    <div className="flex items-center gap-2.5 text-sm font-medium">
                                        <div className="w-8 h-8 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center">
                                            <CalendarIcon className="w-4 h-4" />
                                        </div>
                                        {formattedDueDate}
                                    </div>
                                </div>

                                {/* Creator */}
                                <div className="space-y-2 pt-2 border-t">
                                    <div className="text-[10px] font-bold text-muted-foreground uppercase tracking-widest">Created By</div>
                                    <div className="flex items-center gap-3">
                                        <div className="w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-xs ring-2 ring-white shadow-sm">
                                            {getInitials(task.creator)}
                                        </div>
                                        <div>
                                            <p className="text-sm font-bold leading-tight">{getFullName(task.creator)}</p>
                                            <p className="text-xs text-muted-foreground">{task.creator.email}</p>
                                        </div>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        {/* Assignment List */}
                        <div className="space-y-3 px-1">
                            <h3 className="text-[10px] font-bold uppercase tracking-widest text-muted-foreground flex items-center justify-between">
                                Assignments <Badge variant="outline" className="text-[9px] h-4">{task.executors.length}</Badge>
                            </h3>
                            <div className="space-y-2">
                                {task.executors.length > 0 ? (
                                    task.executors.map((executor) => (
                                        <div key={executor.id} className="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-50 transition-colors border bg-white shadow-sm">
                                            <div className="w-8 h-8 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-[10px]">
                                                {getInitials(executor)}
                                            </div>
                                            <div className="flex-1 min-w-0">
                                                <div className="flex items-center justify-between gap-2">
                                                    <p className="text-xs font-bold truncate">{getFullName(executor)}</p>
                                                    <Badge
                                                        variant="outline"
                                                        className={cn(
                                                            "text-[9px] h-4 px-1.5 uppercase font-bold",
                                                            executor.pivot.status === 'pending' && "bg-slate-50 text-slate-500 border-slate-200",
                                                            executor.pivot.status === 'in_progress' && "bg-blue-50 text-blue-600 border-blue-200",
                                                            executor.pivot.status === 'completed' && "bg-green-50 text-green-600 border-green-200"
                                                        )}
                                                    >
                                                        {executor.pivot.status.replace('_', ' ')}
                                                    </Badge>
                                                </div>
                                                <p className="text-[10px] text-muted-foreground truncate">{executor.email}</p>
                                            </div>
                                        </div>
                                    ))
                                ) : (
                                    <div className="text-center py-6 border border-dashed rounded-lg bg-slate-50/50">
                                        <p className="text-xs text-muted-foreground italic">No team members assigned</p>
                                    </div>
                                )}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
