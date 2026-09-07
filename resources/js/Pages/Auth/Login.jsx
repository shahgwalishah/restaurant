import Checkbox from '@/Components/Checkbox';
import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import GuestLayout from '@/Layouts/GuestLayout';
import { Head, Link, useForm } from '@inertiajs/react';
import { useState } from 'react';

export default function Login({ status, canResetPassword }) {
    const [showPassword, setShowPassword] = useState(false);
    const { data, setData, post, processing, errors, reset } = useForm({
        email: '',
        password: '',
        remember: false,
    });

    const submit = (e) => {
        e.preventDefault();

        post(route('login'), {
            onFinish: () => reset('password'),
        });
    };

    return (
        <GuestLayout>
            <Head title="Staff Login" />

            <div className="mb-8">
                <p className="text-xs font-semibold uppercase tracking-[0.18em] text-[#e75b3d]">
                    Welcome back
                </p>
                <h2 className="mt-2 text-3xl font-bold tracking-tight text-[#1f2824]">
                    Staff login
                </h2>
                <p className="mt-2 text-sm leading-6 text-gray-500">
                    Apne restaurant dashboard mein sign in karein.
                </p>
            </div>

            {status && (
                <div className="mb-4 text-sm font-medium text-green-600">
                    {status}
                </div>
            )}

            <form onSubmit={submit}>
                <div>
                    <InputLabel htmlFor="email" value="Email address" />

                    <TextInput
                        id="email"
                        type="email"
                        name="email"
                        value={data.email}
                        className="mt-1 block w-full"
                        autoComplete="username"
                        placeholder="admin@multanbites.pk"
                        isFocused={true}
                        onChange={(e) => setData('email', e.target.value)}
                    />

                    <InputError message={errors.email} className="mt-2" />
                </div>

                <div className="mt-5">
                    <InputLabel htmlFor="password" value="Password" />

                    <div className="relative mt-1">
                        <TextInput
                            id="password"
                            type={showPassword ? 'text' : 'password'}
                            name="password"
                            value={data.password}
                            className="block w-full pe-12"
                            autoComplete="current-password"
                            placeholder="••••••••"
                            onChange={(e) =>
                                setData('password', e.target.value)
                            }
                        />

                        <button
                            type="button"
                            className="absolute inset-y-0 end-0 flex w-12 items-center justify-center rounded-e-md text-gray-400 transition hover:text-[#e75b3d] focus:outline-none focus:ring-2 focus:ring-[#e75b3d] focus:ring-offset-2"
                            onClick={() => setShowPassword((value) => !value)}
                            aria-label={
                                showPassword
                                    ? 'Hide password'
                                    : 'Show password'
                            }
                            aria-pressed={showPassword}
                        >
                            {showPassword ? (
                                <svg
                                    className="h-5 w-5"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    strokeWidth="1.8"
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    aria-hidden="true"
                                >
                                    <path d="m3 3 18 18" />
                                    <path d="M10.6 10.6a2 2 0 0 0 2.8 2.8" />
                                    <path d="M9.9 4.3A10.8 10.8 0 0 1 12 4c5 0 8.5 4.1 10 8a15.1 15.1 0 0 1-3 4.7" />
                                    <path d="M6.6 6.6A14.7 14.7 0 0 0 2 12c1.5 3.9 5 8 10 8a10.9 10.9 0 0 0 4.1-.8" />
                                </svg>
                            ) : (
                                <svg
                                    className="h-5 w-5"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    strokeWidth="1.8"
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    aria-hidden="true"
                                >
                                    <path d="M2 12s3.5-8 10-8 10 8 10 8-3.5 8-10 8S2 12 2 12Z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                            )}
                        </button>
                    </div>

                    <InputError message={errors.password} className="mt-2" />
                </div>

                <div className="mt-4 block">
                    <label className="flex items-center">
                        <Checkbox
                            name="remember"
                            checked={data.remember}
                            onChange={(e) =>
                                setData('remember', e.target.checked)
                            }
                        />
                        <span className="ms-2 text-sm text-gray-500">
                            Keep me signed in
                        </span>
                    </label>
                </div>

                <div className="mt-6 grid gap-5">
                    {canResetPassword && (
                        <Link
                            href={route('password.request')}
                            className="text-right text-sm font-medium text-[#e75b3d] hover:text-[#c84930] focus:outline-none focus:ring-2 focus:ring-[#e75b3d] focus:ring-offset-2"
                        >
                            Forgot your password?
                        </Link>
                    )}

                    <PrimaryButton
                        className="w-full justify-center rounded-xl bg-[#e75b3d] py-3.5 text-sm normal-case tracking-normal shadow-[0_10px_24px_rgba(231,91,61,0.25)] hover:bg-[#d94f33] focus:bg-[#d94f33] active:bg-[#c84930]"
                        disabled={processing}
                    >
                        {processing
                            ? 'Signing in...'
                            : 'Sign in to dashboard'}
                    </PrimaryButton>
                </div>
            </form>
        </GuestLayout>
    );
}
