export default function ApplicationLogo({ className = '', ...props }) {
    return (
        <img
            src="/images/multan-bites-logo.png"
            alt="Multan Bites"
            className={className}
            {...props}
        />
    );
}
