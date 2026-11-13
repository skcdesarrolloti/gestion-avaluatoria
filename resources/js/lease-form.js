export function leaseForm() {
    return {
        values:{},issues:{},
        init() {
            Object.assign(this,JSON.parse(this.$el.dataset.leaseForm));
        },
        validation(errors) {
            this.issues=errors || {};
            if(this.id) {
                const url=new URL(location.href);url.searchParams.set('lease',this.id);
                history.replaceState(history.state,'',url.href);
            }
            const field=Object.keys(this.issues)[0];
            if(field) this.$nextTick(()=>{
                const control=this.$el.querySelector(`[name="${CSS.escape(field)}"]`);
                for(let parent=control?.parentElement;parent && parent!==this.$el;parent=parent.parentElement)
                    if(parent.tagName==='DETAILS') parent.open=true;
            });
        },
    };
}
