export function photoUpload() {
    return {
        busy: false,
        hasFiles: false,
        fileNames: '',
        update(input) {
            const files = Array.from(input.files || []);
            this.hasFiles = files.length > 0;
            this.fileNames = files.map(file => file.name).join(', ');
        },
        paste(event) {
            const input = this.$refs.photos;
            const items = Array.from(event.clipboardData?.items || []);
            const allowed = ['image/jpeg', 'image/png', 'image/webp'];
            const files = items.filter(item => item.kind === 'file').map(item => item.getAsFile())
                .filter(file => file && allowed.includes(file.type));
            if (!input || files.length === 0) return;
            const transfer = new DataTransfer();
            Array.from(input.files || []).forEach(file => transfer.items.add(file));
            files.forEach((file, index) => {
                const ext = file.type === 'image/png' ? 'png' : (file.type === 'image/webp' ? 'webp' : 'jpg');
                transfer.items.add(new File([file], `foto-pegada-${Date.now()}-${index + 1}.${ext}`, { type: file.type }));
            });
            input.files = transfer.files;
            this.update(input);
            event.preventDefault();
        },
    };
}
